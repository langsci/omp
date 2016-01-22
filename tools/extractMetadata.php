<?php

/**
* extractMetadata.php
* get the metadata of a published submission from the database and save it in a XML file in the DataCite format
* http://schema.datacite.org/meta/kernel-3/doc/DataCite-MetadataKernel_v3.1.pdf
* http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
* http://php.net/manual/en/pdo.query.php
*
* start localhost
* run programm:
* cd /xampp/htdocs/langsci-test/tools
* /xampp/php/php.exe extractMetadata.php 5
*/

// get the submission id from user unput
if(isset($argv[1])){
	$pubFormatId = $argv[1];
	
	// *** get the metadata from the database ***

	// variables for database connection
	$host = 'localhost';
	$database = 'langsci_test';
	$username = 'langsci_test';
	$password = 'TEST!langsci7';

	try {
		// connect with database
		$db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		 
		// sql query to get doi 
		$sqlDoi = 'SELECT ps.submission_id, pf.publication_format_id, pfs.setting_name, pfs.setting_value, sf.file_id, sf.file_type, sf.file_size
			FROM `publication_formats` pf 
			JOIN `published_submissions` ps ON (ps.submission_id = pf.submission_id)
			JOIN `publication_format_settings` pfs ON (pf.publication_format_id = '.$pubFormatId.' AND pfs.publication_format_id = pf.publication_format_id)
			JOIN `submission_files` sf ON (pf.publication_format_id = sf.assoc_id) 
			LEFT JOIN `identification_codes` ic ON (pf.publication_format_id = ic.publication_format_id)  ';
		
		// ask database and handle result
		foreach($db->query($sqlDoi) as $row){
			
			$submissionId = $row['submission_id'];
			
			if ($row['setting_name'] == 'pub-id::doi'){
				$doi = $row['setting_value'];
			}
			
			$size = $row['file_size'];
			
			$format = $row['file_type'];
		
		}
		
		if(!isset($doi)){
			exit('This publication format has no DOI assigned.');
		}
		
		// publication date
		$sqlPublicationDate = 'SELECT date FROM `publication_dates` WHERE publication_format_id='.$pubFormatId;
		
		// ask database and handle result
		foreach($db->query($sqlPublicationDate) as $row){
			
			$publicationDateString = $row['date'];
			
			$publicationYear = substr($publicationDateString, 0, 4);
			$publicationMonth = substr($publicationDateString, 4, 2);
			$publicationDay = substr($publicationDateString, 6, 2);
			
			$publicationDate = $publicationYear . '-' . $publicationMonth . '-' . $publicationDay;
			
		}
		
		// isbn
		$sqlISBN = 'SELECT value FROM `identification_codes` WHERE publication_format_id='.$pubFormatId;
		
		// ask database and handle result
		foreach($db->query($sqlISBN) as $row){
			
			if($row['value']!=''){
				$isbn = $row['value'];
				
			}
			
		}
		
		// locale and series infos
		$sqlLanguage = 'SELECT s.locale, s.series_id, s.series_position, ss.setting_name, ss.setting_value
			FROM `published_submissions` ps 
			JOIN submissions s ON (ps.submission_id = s.submission_id AND ps.submission_id = '.$submissionId.')
			JOIN series_settings ss ON (ss.series_id = s.series_id)';
			
		$series = array(
			"African Language Grammars and Dictionaries" => "",
			"Classics in Linguistics" => "2366-374X",
			"Computational Models of Language Evolution" => "2364-7809",
			"Conceptual Foundations of Language Science" => "",
			"Contemporary African Linguistics" => "",
			"Empirically Oriented Theoretical Morphology and Syntax" => "2366-3529",
			"Implemented Grammars" => "",
			"Language Variation" => "",
			"Monographs on Comparative Niger-Congo" => "",
			"Morphological Investigations" => "",
			"Studies in Caribbean Languages" => "",
			"Studies in Diversity Linguistics" => "2363-5568",
			"Studies in Laboratory Phonology" => "2363-5576",
			"Textbooks in Language Sciences" => "2364-6209",
		);
		
		// get metadata of series
		foreach($db->query($sqlLanguage) as $row){
				
			$language = $row['locale'];
				
			$seriesPosition = $row['series_position'];
				
			switch($row['setting_name']){
				
				case 'title' :

					if($row['setting_value'] != ''){
						$seriesTitle = trim($row['setting_value'],'-');
						
						if(isset($series[$seriesTitle]) && $series[$seriesTitle] != ''){
							$issn = $series[$seriesTitle];
						} 
					}
					break;
			}	
		}
	
		
		// sql query to get submission settings (title, subtitle, etc.)
		$sql = 'SELECT ss.submission_id, ss.locale, ss.setting_name, ss.setting_value
			FROM `published_submissions` ps
			JOIN submissions s ON (ps.submission_id = s.submission_id AND ps.submission_id = '.$submissionId.')
			LEFT JOIN submission_settings ss ON (ps.submission_id = ss.submission_id)';
		
		// ask database and handle result
		foreach($db->query($sql) as $row){
			
			switch($row['setting_name']){
				
				case 'prefix' :
					if($row['setting_value'] != ''){
						$prefix = $row['setting_value'];
					}
					break;
				case 'title' :
					if($row['setting_value'] != ''){
						$title = $row['setting_value'];
					}
					break;
				case 'subtitle' :
					if($row['setting_value'] != ''){
						$subtitle = $row['setting_value'];
					}
					break;
				case 'abstract':
					if($row['setting_value'] != ''){
						$abstract = $row['setting_value'];
					}
					break;
				case 'rights':
					if($row['setting_value'] != ''){
						$rights = $row['setting_value'];
					}
					break;
				case 'coverageGeo':
					if($row['setting_value'] != ''){
						$geo = $row['setting_value'];
					}
					break;
			}

		}
		
		// authors 
		$sqlAuthor = 'SELECT ps.submission_id, a.first_name, a.middle_name, a.last_name
			FROM `published_submissions` ps
			JOIN authors a ON (a.submission_id = ps.submission_id AND ps.submission_id = '.$submissionId.')';
		
		// ask database and handle result
		foreach($db->query($sqlAuthor) as $key => $row){
			
			$authors[$key] = $row['last_name'].', '.$row['first_name'];
			
			if(isset($row['middle_name'])&& $row['middle_name'] != ''){
				$authors[$key] .= ' '.$row['middle_name'];
			}
			
		}
		
		// *** create XML and write to file  ***

		// begin of xml file
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>'.'<resource/>');
		$xml->addAttribute('xmlns','http://datacite.org/schema/kernel-3');
		$xml->addAttribute('stringWillBeCutAwayAutomatically:xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$xml->addAttribute('stringWillBeCutAwayAutomatically:xsi:schemaLocation','http://datacite.org/schema/kernel-3 http://schema.datacite.org/meta/kernel-3/metadata.xsd');
		
		// doi
		$identifier = $xml->addChild('identifier', $doi);
		$identifier->addAttribute('identifierType', 'DOI');
		
		// isbn
		if(isset($isbn)) {
			$xml->addChild('alternateIdentifiers')->addChild('alternateIdentifier',$isbn)->addAttribute('alternateIdentifierType', 'ISBN');
		}
		
		// creators
		$creators = $xml->addChild('creators');
		foreach($authors as $author){
			$creator = $creators->addChild('creator')->addChild('creatorName',$author);
		}
		
		// title with optional prefix and subtitle
		if(isset($prefix)) {
			$title = $prefix.' '.$title;
		}
		
		$titles = $xml->addChild('titles');
		$title = $titles->addChild('title', $title);
	//	$title->addAttribute('xml:lang','en-us');
		
		if(isset($subtitle)){
			$subtitle = $titles->addChild('title',$subtitle)->addAttribute('titleType','Subtitle');
		}
		
		// language
		if(isset($language)){
			$xmlLanguage = $xml->addChild('language',str_replace('_','-',strtolower($language)));
		}
		
		// size
		if(isset($size)){
			$xml->addChild('sizes')->addChild('size',formatSizeUnits($size));
		}
		
		// format
		if(isset($format)){
			$xml->addChild('formats')->addChild('format',$format);
		}
		
		// rights
		if(isset($rights)){
			$rightsUrl = 'http://creativecommons.org/licenses/';
			$rightsVersion = '/4.0/';
			$xml->addChild('rightsList')->addChild('rights',$rights)->addAttribute('rightsURI',$rightsUrl.$rights.$rightsVersion);
		}
		
		// publisher
		$xml->addChild('publisher', 'Language Science Press');
		
		// publication year
		$xml->addChild('publicationYear', $publicationYear);
		
		$xml->addChild('dates')->addChild('date', $publicationDate)->addAttribute('dateType', 'Available');
		
		// abstract (mandatory)
		$descriptions = $xml->addChild('descriptions');
		$descriptions->addChild('description',strip_tags($abstract))->addAttribute('descriptionType','Abstract');
		
		// series information
		if(isset($seriesTitle)){
			$descriptions->addChild('description',strip_tags($seriesTitle))->addAttribute('descriptionType','SeriesInformation');
		}
		
		// series: issn
		if(isset($issn)){
			$relatedIdentifier = $xml->addChild('relatedIdentifiers')->addChild('relatedIdentifier',$issn);
			$relatedIdentifier->addAttribute('relatedIdentifierType','ISSN');
			$relatedIdentifier->addAttribute('relationType','IsPartOf');
		}
		
		// geo
		if(isset($geo)){
			$xmlGeoLocations = $xml->addChild('geoLocations');
			$geoLocations = explode(',', $geo);
			
			foreach($geoLocations as $geoLocation){
				$xmlGeoLocations->addChild('geoLocation')->addChild('geoLocationPlace', trim($geoLocation));
			}
		}
		
		
		// write output to file
		$file = fopen('output.xml', 'w');
		fwrite($file,$xml->asXML());
		fclose($file);
	  
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
}
else{
	echo "Please enter a valid publication format id!";
}


// Snippet from PHP Share: http://www.phpshare.org
function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}

?>

