<?php

/**
 * @file AnnotationsHandler.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AnnotationsHandler
 */

import('classes.handler.Handler');
import('plugins.generic.annotations.AnnotationsDAO');
error_reporting(E_ALL);
ini_set('display_errors', true);

class AnnotationsHandler extends Handler {

	/**
	 * Constructor
	 */
	function AnnotationsHandler() {
		parent::Handler();
	}


	function getPressPath(&$request) {
		$press = $request -> getPress();
		$pressPath = $press -> getPath();
 		$completeUrl = $request->getCompleteUrl();
		return substr($completeUrl,0,strpos($completeUrl,$pressPath)) . $pressPath ;
	}

	/**
	 * Handle view page request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function annotations($args, $request) {

		$press = $request -> getPress();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle','plugins.generic.annotations.title');

		$annotationsDAO = new AnnotationsDAO;
		$fsIds = $annotationsDAO->getFileIds();   // returns matrix with keys=file_id values=submission_id
	
		if ($fsIds==null) {

			$templateMgr->assign('filesFound',false);

		} else {

	 	//	echo "<br>". $request->getCompleteUrl();
		//	echo "<br>". $request->getRequestedPressPath();
		//	echo "<br>". $request->getBaseUrl();

			$fileIds = array_keys($fsIds);
			$submissionIds = array_values($fsIds);

			$publicationFormatIds =  $annotationsDAO->getPublicationFormatIds(implode(",",$fileIds));
			$pressIds =  $annotationsDAO->getPressIds(implode(",",$submissionIds));
			
			$titles = array();
			for ($i=0;$i<sizeof($submissionIds); $i++) {
				$titles[] = $annotationsDAO->getTitle($submissionIds[$i]);
			}
		
	 		echo "<br>file ids: ". implode(",",$fileIds);
			echo "<br>sub ids: ". implode(",",$submissionIds);
			echo "<br>titles: ". implode($titles);


			$fileIds = array_keys($fsIds);
			$submissionIds = array_values($fsIds);		
			$urlTails = array();
			for ($i=0; $i<sizeof($fileIds);$i++) {
				$urlTails[] = "/" . $submissionIds[$i] . "/" . $publicationFormatIds[$i] . "/" . $fileIds[$i] . "-" . $pressIds[$i];
			}

			$templateMgr->assign('filesFound',true);
			$templateMgr->assign('urlTails',$urlTails);
			$templateMgr->assign('titles',$titles);
			$templateMgr->assign('pressPath',$press -> getPath());

		} 

		$annotationsPlugin = PluginRegistry::getPlugin('generic', ANNOTATIONS_PLUGIN_NAME);
		$templateMgr->display($annotationsPlugin->getTemplatePath()."annotations.tpl");
	}


	function file($args, $request) {

		$url = $_POST["file"];

		// Get cURL resource
		$curlGet = curl_init();

		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json;charset=utf8'
		);

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curlGet, array(
		   	CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_ENCODING => "UTF-8",
			CURLOPT_HTTPHEADER=>$headers, 
			CURLOPT_URL => 'https://hypothes.is/api/search?uri='.$url.'&limit=400'
		));

		// Send the get request + get response
		$respGet = curl_exec($curlGet);
		curl_close($curlGet);
		$resultsArrayExt = json_decode($respGet,true);
		$resultsArray = $resultsArrayExt['rows'];


		$tags = array();
		$texts = array();
		$user = array();
		$created = array();
		$references = array();
		$uris = array();
		$documents = array();
		$getItem = array();
		$ids = array();
		$targets = array();

		for ($i=0; $i<sizeof($resultsArray); $i++) {

			$item = $resultsArray[$i];

			$ids[] = $item['id'];
			$user[] = $item['user'];
			$created[] = $item['created'];
			$targets[] = $item['target'];
			if (in_array("references",array_keys($item)) ) {
				$references[] = $item['references'];
			} else {
				$references[] = array();
			}
			if (in_array("document",array_keys($item)) ) {
				$documents[] = $item['document'];
			} else {
				$documents[] = array();
			}
			$tags[] = $item['tags'];
			$texts[] = $item['text'];
			$uris[] = $item['uri'];
		}



		//  Analyse der Uri
		$countUris = array_count_values($uris);
		arsort($countUris);

		// Analyse der Tags
		$allTags = array();
		for ($i=0;$i<sizeof($tags); $i++) {
			$currentTag = $tags[$i];
			for ($ii=0;$ii<sizeof($currentTag); $ii++) {
				$allTags[] = $currentTag[$ii];			
			}		
		}
		$countTags = array_count_values($allTags);
		arsort($countTags);

		// Analyse Datum der Erstellung (created)
		asort($created);

		// Analyse References
		$numberOfCommentsWithReferences = 0;
		for ($i=0;$i<sizeof($references); $i++) {
			if (!empty($references[$i])) {
				$numberOfCommentsWithReferences++;
			}
		} 

		// Analyse Document
		$numberOfCommentsWithDocument = 0;
		for ($i=0;$i<sizeof($documents); $i++) {
			if (!empty($documents[$i])) {
				$numberOfCommentsWithDocument++;
			}
		} 

		// Analyse User
		$countUser = array_count_values($user);
		$keysCountUser = array_keys($countUser);
		for ($i=0;$i<sizeof($countUser); $i++) {			
			$acct = strpos($keysCountUser[$i],":");
			$at   = strpos($keysCountUser[$i],"@");

			$userName = substr($keysCountUser[$i],$acct+1,$at-$acct-1);
			$countUser[$userName] = $countUser[$keysCountUser[$i]];
			unset($countUser[$keysCountUser[$i]]);
		} 
		arsort($countUser);



		// Analyse der Einträge/Texts (doppelte Einträge finden -> Orphans identifizieren)
		$countTexts = array_count_values($texts);
		arsort($countTexts);

		$keysCountUris = array_keys($countUris);
		$fileUrl = "no name";
		for ($i=0;$i<sizeof($countUris); $i++) {
			if (strpos($keysCountUris[$i],"ttp://langsci-press.org")>0 && strpos($keysCountUris[$i],"file")==false) {
				$fileUrl = $keysCountUris[$i];
			}
		}	

		// title
		$tmp = $documents[0];
		$fileTitle = $tmp['title'];


		// prepare and display template
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle','plugins.generic.annotations.file.title');
		$templateMgr->assign('countUser',$countUser);
		$templateMgr->assign('countTags',$countTags);
		$templateMgr->assign('fileTitle',$fileTitle);
		$templateMgr->assign('fileUrl',$fileUrl);
		$templateMgr->assign('noComments',sizeof($user));
		$templateMgr->assign('numberOfCommentsWithReferences',sizeof($numberOfCommentsWithReferences));
		$templateMgr->assign('timeStart', date('l, F jS Y \a\t g:ia', strtotime($created[sizeof($created)-1])));
		$templateMgr->assign('timeEnd', date('l, F jS Y \a\t g:ia', strtotime($created[0])));


		$annotationsPlugin = PluginRegistry::getPlugin('generic', ANNOTATIONS_PLUGIN_NAME);
		$templateMgr->display($annotationsPlugin->getTemplatePath()."file.tpl");


	}



}

?>
