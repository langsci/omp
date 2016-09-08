<?php
/**
* register.php
* script uses DataCite API (https://mds.datacite.org/static/apidoc) 
* to upload the metadata of a DOI to DataCite
* and to register the URL with the doi
* 
* $argv[1] = URL to be registered
* @version 1.2
* @author Svantje Lilienthal - Language Science Press
* @date 2016-02-29
*/

// variables for the connection with datacite
include'credentialsDataCite.php';

// get xml file with the metadata
$fileHandle = fopen("output.xml", "rb");
$fileContents = stream_get_contents($fileHandle);
fclose($fileHandle);

// upload the metadata to datacite
uploadMetadata($username, $password, $fileContents);

// get doi from file output.xml
$xml = simplexml_load_file("output.xml");
$doi = utf8_encode($xml->identifier);

// get URL from user input
$doiUrl = utf8_encode($argv[1]);
$data = "doi=$doi\nurl=$doiUrl";

echo($doi);

// register the URL with the doi
registerURL($username, $password, $data);


/* 
* function upload metadata
* uses DataCite API (https://mds.datacite.org/static/apidoc) to upload the metadata of a DOI to DataCite
* $username, $password, $data string
*/
function uploadMetadata($username, $password, $data){
	
	$url = 'https://mds.datacite.org/metadata';
	
	// set options of http request
	$context_options = array(
		'http' => array(
			'method' => 'POST',
			'header' => "Authorization: Basic " . base64_encode($username.":".$password) . "\r\n".
					"Accept: application/xml;charset=UTF-8"."\r\n".
					"Content-Length: ".strlen($data)."\r\n".
					"Content-Type: application/xml;charset=UTF-8"."\r\n",
			'content' => $data
		),
	);

	// http request
	$context = stream_context_create($context_options);
	$result = file_get_contents($url, false, $context);

	echo($result);
	
}

/* 
* function register url
* uses DataCite API (https://mds.datacite.org/static/apidoc) to register a DOI with a URL with DataCite
* $username, $password, $data string
*/
function registerURL($username, $password, $data){
	
	$url = 'https://mds.datacite.org/doi';

	// set options of http request
	$context_options = array(
		'http' => array(
			'method' => 'POST',
			'header' => "Authorization: Basic " . base64_encode($username.":".$password) . "\r\n".
					"Accept: text/plain;charset=UTF-8"."\r\n".
					"Content-Length: ".strlen($data)."\r\n".
					"Content-Type: text/plain;charset=UTF-8"."\r\n",
			'content' => $data
		),
	);

	// http request
	$context = stream_context_create($context_options);
	$result = file_get_contents($url, false, $context);

	echo($result);
	
}

?>