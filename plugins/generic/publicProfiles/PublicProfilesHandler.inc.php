<?php

/**
 * @file plugins/generic/publicProfiles/PublicProfilesHandler.inc.php
 *
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PublicProfilesHandler
 *
 * Find the content and display the appropriate page
 *
 */

import('classes.handler.Handler');
import('plugins.generic.publicProfiles.PublicProfilesDAO');
import('classes.monograph.MonographDAO');
import('classes.monograph.PublishedMonographDAO');
import('classes.press.SeriesDAO');

class PublicProfilesHandler extends Handler {

	function viewPublicProfile($args, $request) {

		// page is only processed if a user is logged in 
		$user = $request->getUser();
		if (!$user) {
			$request->redirect('index');
		}

		// get profile user id from url
		$userId = substr($request->getCompleteUrl(),-strpos(strrev($request->getCompleteUrl()),"/"));
		if (!ctype_digit ($userId)) {
			$request->redirect('index');
		} 

		// get setting variables
		$press = $request -> getPress();
		$showProfile = false;
		$showEmail = false;
		$publicProfilesDAO = new PublicProfilesDAO;
		$existsLangsciWebsiteSettings = $publicProfilesDAO->existsTable('langsci_website_settings');
		$onlyPublishedSubmissions = $press->getSetting('langsci_publicProfiles_onlyPublishedMonographs');
		$unifiedStyleSheetForLinguistics = $press->getSetting('langsci_publicProfiles_unifiedStyleSheetForLinguistics');
		$userGroupsString = $press->getSetting('langsci_publicProfiles_userGroups');
		$completePressPath = $this->getPressPath($request);

		if (!$existsLangsciWebsiteSettings) {
			$showProfile = true;
		} else {
			$showProfile = $publicProfilesDAO->getUserSetting($userId,'PublicProfile')=='true';
			$showEmail = $publicProfilesDAO->getUserSetting($userId,'Email')=='true';
		}

		// print books the user worked on
		$bookAchievements = "";

		$userGroupsArray = explode(",",$userGroupsString); 
		if ($userGroupsString=="") {
			$userGroupsArray = array();
		}

		for ($i=0; $i<sizeof($userGroupsArray); $i++) {


			$userGroupName = trim($userGroupsArray[$i]);

			$userGroupId = $publicProfilesDAO -> getUserGroupIdByName($userGroupName);

			if ($userGroupId) {
				$submissions = $publicProfilesDAO->getSubmissionsFromStageAssignments($userId, $userGroupId,
														$onlyPublishedSubmissions);
			} else {
				$submissions = array();
			}

			if (sizeof($submissions)>0) {
				$bookAchievements .= "<div><p><span class='header'>Worked on the following books as " . $userGroupName . ":</span></p><ul>";
			}

			for ($ii=0; $ii<sizeof($submissions); $ii++) {
				$submissionString="";
							if ($unifiedStyleSheetForLinguistics) {
								$submissionString =
									$this->biblioHtmlListElement(		
											$this->getBiblioLinguistStyle($submissions[$ii],$completePressPath),
											$submissions[$ii],
											$completePressPath,
											true);
							} else {
								$submissionString = 
									$this->biblioHtmlListElement(
											$this->getNameOfSubmission($submissions[$ii],$completePressPath),
											$submissions[$ii],
											$completePressPath,
											false);								
							}
				$bookAchievements .= $submissionString;
			}

			if (sizeof($submissions)>0) {
				$bookAchievements .= "</ul></div>";
			}
			
		}

		$templateMgr = $this->getTemplateManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.publicProfiles.title');
		$templateMgr->assign('showProfile', $showProfile);

		if ($showProfile) {

			$userData =  $publicProfilesDAO -> getUserData($userId);
			$templateMgr->assign('username',       $userData[0]);
			$templateMgr->assign('first_name',     $userData[1]);
			$templateMgr->assign('last_name',      $userData[2]);
			$templateMgr->assign('url',            $userData[3]);
			$templateMgr->assign('email',          $userData[4]);
			$templateMgr->assign('academic_title', $userData[5]);
			$templateMgr->assign('affiliation',    $userData[6]);
			$templateMgr->assign('biostatement',   $userData[7]);
			$templateMgr->assign('imagePath', "" . $request->getBaseUrl() ."/plugins/generic/userWebsiteSettings/profileImg/".$userId.".jpg");
			$templateMgr->assign('showEmail',      $showEmail);
			$templateMgr->assign('bookAchievements', $bookAchievements);
		}

		$publicProfilesPlugin = PluginRegistry::getPlugin('generic', PUBLICPROFILES_PLUGIN_NAME);
		$templateMgr->display($publicProfilesPlugin->getTemplatePath().'publicProfile.tpl');
	}

	function getTemplateManager($request)	{
		$this->validate();
		$press = $request->getPress();
		$this->setupTemplate($request, $press);
		$templateMgr = TemplateManager::getManager($request);	
		return $templateMgr;
	}

	function getPressPath(&$request) {
		$press = $request -> getPress();
		$pressPath = $press -> getPath();
 		$completeUrl = $request->getCompleteUrl();
		return substr($completeUrl,0,strpos($completeUrl,$pressPath)) . $pressPath ;
	}

	function getNameOfSubmission($submission_id, $completePressPath) {

		$monographDAO = new MonographDAO;
		$monograph = $monographDAO -> getById($submission_id);

		if (!$monograph) {
			return "Invalid monograph id " . $submission_id;
		}

		$authors = $monograph -> getAuthorString();
			
		if ($authors=="") {
			$authors = "N.N. ";
		}
		$title   = $monograph -> getLocalizedFullTitle(); 
		$bookPath = $completePressPath . "/catalog/book/" . $submission_id;

		return $authors . ": <a href='".$bookPath."'>" . $title . "</a>";
	}

	function getBiblioLinguistStyle($submission_id, $completePressPath) {

		$biblio = "";
		$publicProfilesDAO = new PublicProfilesDAO;

		// get monograph and authors object
		$isPublished = false;
		$monographDAO = new PublishedMonographDAO;
 		$monograph = $monographDAO -> getById($submission_id);
		if ($monograph) {
			$isPublished = true;
		} else {
			$monographDAO = new MonographDAO;
 			$monograph = $monographDAO -> getById($submission_id);
			if (!$monograph) {
				return "Invalid  monograph id: " . $submission_id;
			}
		}
		$authors = $monograph->getAuthors();

		// get series information				
		$seriesId = $monograph ->getSeriesId();
		$seriesDAO = new SeriesDAO;
		$series = $seriesDAO -> getById($seriesId,1);
		if (!$series) {
			$seriesTitle = "No Series Selected";
			$seriesPosition="tba";
		} else {
			$seriesTitle = $series->getLocalizedFullTitle();
			$seriesPosition = $monograph ->getSeriesPosition();
			if (empty($seriesPosition)) {
				$seriesPosition="tba";
			}
		}

		// is edited volume (if there is at least one volume editor)
		$editedVolume = false;
		for ($ii=0; $ii<sizeof($authors); $ii++) {
			if ($authors[$ii]->getUserGroupId()==$publicProfilesDAO->getUserGroupIdByName("Volume Editor")) {
				$editedVolume=true;
			}
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();
		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();
			if ($editedVolume && $userGroupId==$publicProfilesDAO->getUserGroupIdByName("Volume Editor")) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$publicProfilesDAO->getUserGroupIdByName("Author"))  {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			}
		}

		// get author string
		$authorString=""; 
		for ($i=0; $i<sizeof($authorsInBiblio); $i++) {

			// format for first author: last_name, first_mane, for all others: first_name last_name
			// to do middle name
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
	
		// get year of publication (of pdf), only for published mongraphs
		$publicationDateString = "????";
		if ($isPublished) {

			$pubformats = $monograph->getPublicationFormats();
			$pdf = false;

			if (sizeof($pubformats)>0) {
				for ($i=0; $i<sizeof($pubformats); $i++) {
					$formatName = implode($pubformats[$i]->getName());
					if ($formatName="PDF") {
						$pubdates = $pubformats[$i] -> getPublicationDates();
						$pubdatesArray = $pubdates->toArray();
						for ($ii=0;$ii<sizeof($pubdatesArray);$ii++) {
							if ($pubdatesArray[$ii] ->getRole()=="01") {
								$publicationDateString = substr($pubdatesArray[$ii] ->getDate(),0,4);
							}
						}
					}
				}
			}
		}

		// get title
		$title = $monograph->getLocalizedFullTitle($submission_id);
		if (!$title) {
			$title = "Title unknown";
		}				

		// compose biblio string
		$biblio = $authorString . $publicationDateString .
				".<i> " . $title . "</i> (".$seriesTitle  . " " . $seriesPosition ."). Berlin: Language Science Press.";
		
		return $biblio;
	}	

	function biblioHtmlListElement($biblio,$submission_id,$completePressPath,$addLink) {
		
		if ($addLink) {
			$bookPath = $completePressPath . "/catalog/book/".$submission_id;
			$returner = "<li>".$biblio." <a href='".$bookPath."'>view catalog entry</a></li>";
		} else {
			$returner = "<li>".$biblio."</li>";
		}
		return $returner;
	}	


}

?>
