<?php

	// get 'Publication date' for publication format 'Complete book'
	function getPublicationDate($submissionId) {

		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		if ($publishedMonograph) {

			$pubformats = $publishedMonograph->getPublicationFormats();
			for ($i=0; $i<sizeof($pubformats); $i++) {

				$formatName = implode($pubformats[$i]->getName());
				if ($formatName=="Complete book") {

					$pubdates = $pubformats[$i]->getPublicationDates();
					$pubdatesArray = $pubdates->toArray();
					for ($ii=0;$ii<sizeof($pubdatesArray);$ii++) {
						if ($pubdatesArray[$ii]->getRole()=="01") {
							return $pubdatesArray[$ii]->getDate();
						}
					}
				}
			}
		}
		return null;
	}

	function getSubmissionPresentationString($submissionId) {

		$langsciCommonDAO = new LangsciCommonDAO;

		// get monograph and authors object
		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		$monographObject = $publishedMonograph;
		if (!$publishedMonograph) {
			$monographDAO = new MonographDAO;
 			$monograph = $monographDAO -> getById($submissionId);
			if (!$monograph) {
				return "Invalid  monograph id: " . $submissionId;
			}
			$monographObject = $monograph;
		}
		$authors = $monographObject->getAuthors();

		// is edited volume (if there is at least one volume editor)
		$editedVolume = false;
		for ($i=0; $i<sizeof($authors); $i++) {
			if ($authors[$i]->getUserGroupId()==$langsciCommonDAO->getUserGroupIdByName("Volume Editor")) {
				$editedVolume=true;
			}
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();

		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();
			if ($editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Volume Editor")) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Author"))  {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			}
		}

		// get author string
		$authorString=""; 
		for ($i=0; $i<sizeof($authorsInBiblio); $i++) {
			if ($i>0) {
				$authorString = $authorString . ", ";
			}
			$authorString = $authorString .
				$authorsInBiblio[$i]->getFirstName() . " " .  $authorsInBiblio[$i]->getLastName();
		}
			
		if ($authorString=="") {
			$authorString = "N.N. ";
		}
		$title = $monographObject->getLocalizedFullTitle(); 

		return $authorString . ": " . $title ."." ;
	}

	function getBiblioLinguistStyle($submissionId) {

		$langsciCommonDAO = new LangsciCommonDAO;

		// get monograph and authors object
		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		$monographObject = $publishedMonograph;
		if (!$publishedMonograph) {
			$monographDAO = new MonographDAO;
 			$monograph = $monographDAO -> getById($submissionId);
			if (!$monograph) {
				return "Invalid  monograph id: " . $submissionId;
			}
			$monographObject = $monograph;
		}
		$authors = $monographObject->getAuthors();

		// get series information				
		$seriesId = $monographObject->getSeriesId();
		$seriesDAO = new SeriesDAO;
		$series = $seriesDAO -> getById($seriesId,1);
		if (!$series) {
			$seriesTitle = "Series unknown";
			$seriesPosition="tba";
		} else {
			$seriesTitle = $series->getLocalizedFullTitle();
			$seriesPosition = $monographObject ->getSeriesPosition();
			if (empty($seriesPosition)) {
				$seriesPosition="tba";
			}
		}

		// is edited volume (if there is at least one volume editor)
		$editedVolume = false;
		for ($i=0; $i<sizeof($authors); $i++) {
			if ($authors[$i]->getUserGroupId()==$langsciCommonDAO->getUserGroupIdByName("Volume Editor")) {
				$editedVolume=true;
			}
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();
		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();
			if ($editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Volume Editor")) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Author"))  {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			}
		}

		// get author string
		$authorString=""; 
		for ($i=0; $i<sizeof($authorsInBiblio); $i++) {

			// format for first author: last_name, first_name, for all others: first_name last_name
			if ($i==0) {
				$authorString = $authorString .
					$authorsInBiblio[$i]->getLastName() . ", " .  $authorsInBiblio[$i]->getFirstName();
			} else {	
				// separator between authors
				if ($i==$numberOfAuthors-1) {
					$authorString = $authorString . " & ";
				} else {
					$authorString = $authorString . ", ";												
				}
				$authorString = $authorString .
					$authorsInBiblio[$i]->getFirstName() . " " . $authorsInBiblio[$i]->getLastName();
			}
		}

		// get author string: for edited volumes: add (ed.)/(eds.)	
		if ($editedVolume && $numberOfAuthors==1) {
			$authorString = $authorString . " (ed.)";
		} else if ($editedVolume && $numberOfAuthors>1) {
			$authorString = $authorString . " (eds.)";
		}
		$authorString = $authorString . ". ";

		// get author string: if there are no authors: add N.N.		
		if ($authorString==". ") {
			$authorString = "N.N. ";
		}
	
		// get year of publication, only for published mongraphs
		$publicationDateString = getPublicationDate($submissionId);
		if (!$publicationDateString) {
			$publicationDateString = "????";
		} else {
			$publicationDateString = substr($publicationDateString,0,4); 
		}

		// get title
		$title = $monographObject->getLocalizedFullTitle($submissionId);
		if (!$title) {
			$title = "Title unknown";
		}				

		// compose biblio string
		$biblioLinguisticStyle = $authorString . $publicationDateString .
				".<i> " . $title . "</i> (".$seriesTitle  . " " . $seriesPosition ."). Berlin: Language Science Press.";
		
		return $biblioLinguisticStyle;
	}	


?>
