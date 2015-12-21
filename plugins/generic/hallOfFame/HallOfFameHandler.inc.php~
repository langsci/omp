<?php

/**
 * @file hallOfFameHandler.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameHandler
 */

import('classes.handler.Handler');
import('plugins.generic.hallOfFame.HallOfFameDAO');
import('classes.monograph.MonographDAO');
import('classes.monograph.PublishedMonographDAO');
import('classes.press.SeriesDAO');

class HallOfFameHandler extends Handler {

	/** @var StaticPagesPlugin The static pages plugin */
	static $plugin;

	/**
	 * Constructor
	 */
	function HallOfFameHandler() {
		parent::Handler();
	}

	function getPressPath(&$request) {
		$press = $request -> getPress();
		$pressPath = $press -> getPath();
 		$completeUrl = $request->getCompleteUrl();
		return substr($completeUrl,0,strpos($completeUrl,$pressPath)) . $pressPath ;
	}
/*

	function getOmpUrl(&$request) {
		return $request->getBaseUrl();
	}
*/
	/**
	 * Handle view page request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function viewHallOfFame($args, $request) {

		$hallOfFameDAO = new HallOfFameDAO;

		$press = $request -> getPress();
		$userGroupsString = $press->getSetting('langsci_hallOfFame_userGroups');
		$userGroupsArray = explode(",",$userGroupsString); 

		$completePressPath = $this->getPressPath($request);

		$onlyPublishedSubmissions = $press->getSetting('langsci_hallOfFame_onlyPublishedMonographs');
		$unifiedStyleSheetForLinguistics = $press->getSetting('langsci_hallOfFame_unifiedStyleSheetForLinguistics');
		$linksToPublicProfile = $press->getSetting('langsci_hallOfFame_linksToPublicProfile');
	
		$existsLangSciSettings = $hallOfFameDAO->existsTable("langsci_website_settings");
		$baseUrl = $request->getBaseUrl();

		$htmlContent = "";
		$htmlIntro1 = "<p id='intro'>The following people already supported our press (go to the section for";
		$htmlIntro2 = "). Each star signifies one monograph. Click on the name to get the list of the monograph(s) a person worked on.</p>";

		$nameAccordion = array();

		// for all elements in setting string
		for ($i=0; $i<sizeof($userGroupsArray); $i++) {

			$userGroupName = trim($userGroupsArray[$i]);
			$htmlContent .= "<h3 class='userGroup'><a class='userGroup' name='".$userGroupName."'>".$userGroupName."</a></h3>";
			$htmlIntro1 .= " <a href='#".$userGroupName."'>".$userGroupName."s</a>";
			$userGroupId = $hallOfFameDAO -> getUserGroupIdByName($userGroupName);

			// for all existing user groups
			if ($userGroupId) {

				$htmlContentUserGroup = "";
				$numberOfUsers = 0;
								
				// user group
				$userRanking = $hallOfFameDAO -> getUserRanking($userGroupId,$onlyPublishedSubmissions);

				// for all users
				for ($ii=0; $ii<sizeof($userRanking); $ii++) {

					$userId = $userRanking[$ii][1];

					// check if person wants to be include in the hall of fame
					$includeUser = true;
					if ($existsLangSciSettings) {

						$includeUser = $hallOfFameDAO->getUserSetting($userId,"HallOfFame")=="true";
					}

					if ($includeUser) {

						$numberOfUsers++;
						$nameOfUser = $hallOfFameDAO -> getNameOfUser($userId);

						if ($nameOfUser==null) {
							$nameOfUser = "N.N.";		
						}

						// add stars
						$numberOfStars = $userRanking[$ii][0];
						$pos  = 350;
						$width = $pos  + $numberOfStars*25;

						// user name
						$addLinkToProfile = false;
						if ($existsLangSciSettings) {
							$addLinkToProfile = $hallOfFameDAO->getUserSetting($userId,"PublicProfile")=="true";
						}
  
					   $htmlContent .= "<div class='hallOfFameAccordion'>";
						$userEntry = "";
						if ($linksToPublicProfile && $addLinkToProfile) {
							$pathToProfiles = $press->getSetting('langsci_publicProfiles_path');

							$userEntry .= "<h3 style='background-image: url(&#39;".$baseUrl."/plugins/generic/hallOfFame/img/goldsmall.png&#39;) !important;
												background-repeat:no-repeat !important;background-position: ".$pos."px 2px !important;
												width: ".$width."px !important;'><a>".$nameOfUser."</a>
												<a class='linkPublicProfile' href='../".$pathToProfiles."/".$userId."' >view profile</a></h3>" ;

						} else {
							$userEntry .= "<h3 style='background-image: url(&#39;".$baseUrl."/plugins/generic/hallOfFame/img/goldsmall.png&#39;) !important;
												background-repeat:no-repeat !important;background-position: ".$pos."px 2px !important;
												width: ".$width."px !important;'><a>".$nameOfUser."</a></h3>" ;
						} 

						$htmlContent .= $userEntry;



						// submissions
						$submissions = $hallOfFameDAO->getSubmissionsFromStageAssignments($userId, $userGroupId,
														$onlyPublishedSubmissions);

						$htmlContentUserGroup = "<div class='lsp_content'><ul>";
						for ($iii=0; $iii<sizeof($submissions); $iii++) {
							$submissionString="";
							if ($unifiedStyleSheetForLinguistics) {

								$submissionString =
									$this->biblioHtmlListElement(		
											$this->getBiblioLinguistStyle($submissions[$iii],$completePressPath),
											$submissions[$iii],
											$completePressPath,
											true);
							} else {
								$submissionString = 
									$this->biblioHtmlListElement(
											$this->getNameOfSubmission($submissions[$iii],$completePressPath),
											$submissions[$iii],
											$completePressPath,
											false);								
							}
							$htmlContentUserGroup .= $submissionString ;
						}
						$htmlContentUserGroup .= "</ul></div>";
						$htmlContent .= $htmlContentUserGroup;

						$htmlContent .= "</div>";
					}
				}
			}

		}

		$htmlContent = $htmlIntro1 . $htmlIntro2 . $htmlContent;

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('htmlContent',$htmlContent);
		$templateMgr->assign('pageTitle','plugins.generic.hallOfFame.title');
		$hallOfFamePlugin = PluginRegistry::getPlugin('generic', HALLOFFAME_PLUGIN_NAME);
		$templateMgr->display($hallOfFamePlugin->getTemplatePath()."hallOfFame.tpl");
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
		$hallOfFameDAO = new HallOfFameDAO;

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
			if ($authors[$ii]->getUserGroupId()==$hallOfFameDAO->getUserGroupIdByName("Volume Editor")) {
				$editedVolume=true;
			}
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();
		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();
			if ($editedVolume && $userGroupId==$hallOfFameDAO->getUserGroupIdByName("Volume Editor")) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$hallOfFameDAO->getUserGroupIdByName("Author"))  {
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
	
		// get year of publication (of complete book), only for published mongraphs
		$publicationDateString = "????";
		if ($isPublished) {

			$pubformats = $monograph->getPublicationFormats();
			$pdf = false;

			if (sizeof($pubformats)>0) {
				for ($i=0; $i<sizeof($pubformats); $i++) {
					$formatName = implode($pubformats[$i]->getName());

					if ($formatName=="Complete book") {
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
