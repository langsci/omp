<?php

/**
 * @file StaticPagesHandler.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.staticPages
 * @class StaticPagesHandler
 * Find static page content and display it when requested.
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
		$settingString = $press->getSetting('userGroupsHallOfFame');
		$settingArray = explode(",",$settingString); 

		$completePressPath = $this->getPressPath($request);

		$onlyPublishedSubmissions = $press->getSetting('onlyPublishedMonographs');
		$unifiedStyleSheetForLinguistics = $press->getSetting('unifiedStyleSheetForLinguistics');
		$linksToPublicProfile = $press->getSetting('linksToPublicProfile');
		$existsLangSciSettings = $hallOfFameDAO->existsLangSciSettings();

		$htmlContent = "";
		$htmlContent .= "<table>";

		// for all elements in setting string
		for ($i=0; $i<sizeof($settingArray); $i++) {

			$userGroupId = $hallOfFameDAO -> getUserGroupIdByName(trim($settingArray[$i]));

			// for all existing user groups
			if ($userGroupId) {

				// user group
				$userRanking = $hallOfFameDAO -> getUserRanking($userGroupId,$onlyPublishedSubmissions);

				if (sizeof($userRanking)>0) {
					$htmlContent .= "<tr><td class='userGroup'>".trim($settingArray[$i]).
									"</td><td></td></tr>";
				}

				// for all users
				for ($ii=0; $ii<sizeof($userRanking); $ii++) {

					$userId = $userRanking[$ii][1];

					// check if person wants to be include in the hall of fame
					$includeUser = "true";
					if ($existsLangSciSettings) {
						$includeUser = $hallOfFameDAO->getUserSetting($userId,"hall of fame");
					}

					if ($includeUser=="true") {

						$nameOfUser = $hallOfFameDAO -> getNameOfUser($userId);

						if ($nameOfUser==null) {
							$nameOfUser = "N.N.";		
						}
						$blockId = $userId . "_" . $userGroupId;

						// button to open and close submission view
						$button = '<button class="showSubmissions" id="'.$blockId . "_img".'"
										type="button" onclick="showSubmissions(\''.$blockId.'\')"></button>';

						// add stars
						$numberOfStars = $userRanking[$ii][0];
						$numberOfStarsInARow = 20;
						$widthOneStar = 15;
						$widthStars1 = $numberOfStarsInARow*$widthOneStar;
						$heightStars1 = floor($numberOfStars/$numberOfStarsInARow)*$widthOneStar;
						$widthStars2 = $numberOfStars%$numberOfStarsInARow*$widthOneStar;
						$heightStars2 = $widthOneStar;
						$heightStarsContainer = $heightStars1+$heightStars2;

						$divStars  = '<div style="display:inline;width:150px;
												height: '.$heightStarsContainer.'px;">
											<div id="stars1" style="width:'.$widthStars1.'px;height:'.$heightStars1.'px;"></div>
											<div id="stars2" style="width:'.$widthStars2.'px;height:'.$heightStars2.'px;"></div>
									  </div>';


						// user name
						$addLinkToProfile = "false";
						if ($existsLangSciSettings) {
							$addLinkToProfile = $hallOfFameDAO->getUserSetting($userId,"public profile");
						}

						if ($linksToPublicProfile && $addLinkToProfile) {
							$htmlContent .= "<tr class='users'><td>". $button
								. "<a href='" . $completePressPath . "/user/publicProfile/" . $userId . "'>" . $nameOfUser ."</a>"
								."</td><td>".$divStars."</td></tr>";
						} else {
							$htmlContent .= "<tr class='users'><td>".$button.$nameOfUser."</td>".
											"<td>".$divStars."</td></tr>";
						}

						// submissions
						$submissions = $hallOfFameDAO->getSubmissionsFromStageAssignments($userId, $userGroupId,
														$onlyPublishedSubmissions);

						$htmlContent .= "<tr class='submissions' id='".$blockId."' ><td colspan='3'><ul>";
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
							$htmlContent .= $submissionString ;
						}
						$htmlContent .= "</ul></td></tr>";
					}
				}
			}
		}
		$htmlContent .= "</table>"; 

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
