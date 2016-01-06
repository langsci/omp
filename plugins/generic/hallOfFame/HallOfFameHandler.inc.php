<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameHandler.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameHandler
 */

import('classes.handler.Handler');
import('plugins.generic.hallOfFame.HallOfFameDAO');
import('plugins.generic.hallOfFame.LangsciCommonDAO');
import('classes.monograph.MonographDAO');
import('classes.monograph.PublishedMonographDAO');
import('classes.press.SeriesDAO');

include('LangsciCommonFunctions.inc.php');

class HallOfFameHandler extends Handler {

	private $prizes = array('gold','silver','bronze','series','recent');

	function HallOfFameHandler() {
		parent::Handler();
	}

	function viewHallOfFame($args, $request) {

		$press = $request -> getPress();
		$hallOfFameDAO = new HallOfFameDAO;
		$langsciCommonDAO = new LangsciCommonDAO;
		$userDao = DAORegistry::getDAO('UserDAO');

		// get setting parameters
		$settingPath = $press->getSetting('langsci_hallOfFame_path');
		$settingUserGroups = $press->getSetting('langsci_hallOfFame_userGroups');
		$settingUnifiedStyleSheetForLinguistics = $press->getSetting('langsci_hallOfFame_unifiedStyleSheetForLinguistics');
		$settingLinksToPublicProfile = $press->getSetting('langsci_hallOfFame_linksToPublicProfile');
		$settingStartCounting = $press->getSetting('langsci_hallOfFame_startCounting');
		$settingRecency = $press->getSetting('langsci_hallOfFame_recentDate');
		$settingMinNumberOfSeries = $press->getSetting('langsci_hallOfFame_minNumberOfSeries');
		$settingPercentileRanks = $press->getSetting('langsci_hallOfFame_percentileRanks');
		$settingMedalCount = $press->getSetting('langsci_hallOfFame_medalCount');
		$settingIncludeCommentators = $press->getSetting('langsci_hallOfFame_includeCommentators');

		// check and transform setting parameters   todo: hier default-Werte?
		$userGroupsArray = explode(",",$settingUserGroups);
		if ($settingUserGroups=="") {
			$userGroupsArray = array();
		}
		$settingPercentileRanksArray = explode(",",$settingPercentileRanks);
		if ($settingPercentileRanks=="") {
			$settingPercentileRanksArray = array();
		}
		// empty string or no number: all users will be display in the medal count
		if (!ctype_digit($settingMedalCount)) {
			$settingMedalCount='';
		}
		$recencyDate=null;
		if (ctype_digit($settingRecency)) {
			$recencyDate = new DateTime();
			$recencyDate->sub(new DateInterval('P'.$settingRecency.'M'));
		}

		// get data for the hall of fame
		$userGroups = array();
		$medalCount = array();
		$userGroupNames = array();
		$maxNameLength=0;
		for ($i=0; $i<sizeof($userGroupsArray); $i++) {

			// get user group
			$userGroupName = trim($userGroupsArray[$i]);
			$userGroupId = $langsciCommonDAO->getUserGroupIdByName($userGroupName);

			if ($userGroupId) {

			$userGroups[$i]['userGroupId'] = $userGroupId;
			$userGroups[$i]['userGroupName'] = $userGroupName;

			$userGroupNames[$userGroupId] = $userGroupName;

			// get achievements of this user group
			$achievements = $hallOfFameDAO->getAchievements($userGroupId);

			// remove userIds who do not exists anymore
			$this->removeNonExistingUsers($achievements);

			// remove users who do not want to be listed in the hall of fame
			$this->removeAnonymousUsers($achievements);

			// remove submissions that where published before date x
			if (strlen($settingStartCounting)==8 && ctype_digit($settingStartCounting)) {
				$this->removeSubmissionsBeforeDate($achievements,$settingStartCounting);
			}

			// get rank percentile for all users 
			$rankPercentiles = $this->getRankPercentiles($achievements);

			// get number of achievements for each user
			$numberOfAchievements = $this->getNumberOfAchievements($achievements);

			// get users who have achievements in max number of series
			$maxSeriesUsers = array();
			if (ctype_digit($settingMinNumberOfSeries)) {
				$maxSeriesResults = $this->getMaxSeriesUsers($achievements);
				$userGroups[$i]['maxSeries'] = $maxSeriesResults['maxSeries']; 
				if ($userGroups[$i]['maxSeries']>=$settingMinNumberOfSeries) {
					$maxSeriesUsers = $maxSeriesResults['maxSeriesUsers'];
				}
			}
 
			// get users who have max achievements since $recencyDate
			$recentMaxAchievementUsers = array();
			if ($recencyDate) {
				$recentMaxAchievementResults = $this->getMaxAchievementUsers($achievements,$recencyDate->format('Ymd'));
				$userGroups[$i]['maxRecentAchievements']= $recentMaxAchievementResults['maxAchievements'];
				$recentMaxAchievementUsers = $recentMaxAchievementResults['maxAchievementUsers'];
			}

			$userData = array();
			$userData['gold'] = array();
			$userData['silver'] = array();
			$userData['bronze'] = array();
			$keys = array_keys($achievements);
			for ($ii=0; $ii<sizeof($achievements); $ii++) { 

				$userId = $achievements[$keys[$ii]]['user_id'];
				$submissionId = $achievements[$keys[$ii]]['submission_id'];
				$user = $userDao->getById($userId);

				$numberOfAchievementsUser = $numberOfAchievements[$userId];
				$rankPercentile = round($rankPercentiles[$numberOfAchievementsUser],1);
				$maxNameLength = max($maxNameLength,strlen($user->getFirstName(). " " . $user->getLastName()));
				if ($maxNameLength>19) {
					$maxNameLength = 19;
				}

				$linkToProfile = false;
				if ($langsciCommonDAO->existsTable('langsci_website_settings') &&
					$langsciCommonDAO->getUserSetting($userId,'publicProfile')) {
					
					// get path to the public profile
					$pathPublicProfiles = explode("/", $press->getSetting('langsci_publicProfiles_path'));
					$tail = "";
					for ($iii=3; $iii<sizeof($pathPublicProfiles);$iii++) {
						$tail = $tail."/".$pathHallOfFame[$i];
					}
					$linkToProfile = $request->url($press,$pathPublicProfiles[0],$pathPublicProfiles[1],$pathPublicProfiles[2]).$tail."/".$userId;
				}

				if (!strcmp($settingMedalCount,'0')==0 && $medalCount[$userId]==null) {
					$this->initializeMedalCount($medalCount,$userId,$user,$linkToProfile);
				}

				// get medal for this user in this user group
				$medal = 'bronze';
				if ($rankPercentile<=$settingPercentileRanksArray[0]) {
					$medal = 'gold';
				} else if ($rankPercentile<=$settingPercentileRanksArray[1]) {
					$medal = 'silver';
				}
				if (!strcmp($settingMedalCount,'0')==0) {
					$medalCount[$userId]['type'][$medal][$userGroupId]=true;
				}

				// get user data
				$userData[$medal]['user'][$userId]['rankPercentile'] = 100-round($rankPercentile,0);
				$userData[$medal]['user'][$userId]['userId'] = $userId;
				$userData[$medal]['user'][$userId]['fullName'] = $user->getFirstName(). " " . $user->getLastName();
				$userData[$medal]['user'][$userId]['lastName'] = $user->getLastName();
				$userData[$medal]['user'][$userId]['linkToProfile'] = $linkToProfile;

				// get submission data
				$userData[$medal]['user'][$userId]['submissionId'] .= $submissionId;
				$userData[$medal]['user'][$userId]['numberOfSubmissions']++;
				if ($settingUnifiedStyleSheetForLinguistics) {   // to do: funktion getBiblioLinguistStyle ohne path
					$userData[$medal]['user'][$userId]['submissions'][$submissionId]['name'] =
						getBiblioLinguistStyle($submissionId);
				} else {
					$userData[$medal]['user'][$userId]['submissions'][$submissionId]['name'] =
						getSubmissionPresentationString($submissionId);
				}
				$userData[$medal]['user'][$userId]['submissions'][$submissionId]['path'] =
						$request->url($press,'catalog','book',$submissionId);

				// get users with a series star
				$userData[$medal]['user'][$userId]['maxSeriesUser'] = false;
				if (in_array($userId,$maxSeriesUsers)) {
					$userData[$medal]['user'][$userId]['maxSeriesUser'] = true;
					if (!strcmp($settingMedalCount,'0')==0) {
						$medalCount[$userId]['type']['series'][$userGroupId] = true;
					}
				}

				// get users with a recent star
				if (in_array($userId,$recentMaxAchievementUsers)) {
					$userData[$medal]['user'][$userId]['recentMaxAchievementUser'] = true;
					if (!strcmp($settingMedalCount,'0')==0) {
						$medalCount[$userId]['type']['recent'][$userGroupId]=true;
					}
				}

			}

			// get number of prizes for each user
			if (!strcmp($settingMedalCount,'0')==0) {
				$keys = array_keys($medalCount);
				for ($ii=0; $ii<sizeof($medalCount); $ii++) {
					for ($iii=0; $iii<sizeof($this->prizes); $iii++) {
						if ($medalCount[$keys[$ii]]['type'][$this->prizes[$iii]][$userGroupId]) {
							$medalCount[$keys[$ii]]['numberOf'.$this->prizes[$iii]]++;

						}
					}
				}
			}

			usort($userData['gold']['user'],'sort_users');
			usort($userData['silver']['user'],'sort_users');
			usort($userData['bronze']['user'],'sort_users');

			$userGroups[$i]['userData'] = $userData;
			$userGroups[$i]['maxAchievements'] = max($numberOfAchievements);

			} // end if ($userGroupId)
		} // end for ($i=0; $i<sizeof($userGroupsArray); $i++)

		if (!strcmp($settingMedalCount,'0')==0) {

			uasort($medalCount,'sort_for_medal_count');
			// only display a certain number of users in the medal count?
			if (!strcmp($settingMedalCount,'')==0) {
				$keys = array_keys($medalCount);
				$end = sizeof($medalCount);
				for ($i=$settingMedalCount; $i<$end; $i++) {
					unset($medalCount[$keys[$i]]);
				}
			}
			// get medal count ranks
			$this->getMedalCountRanks($medalCount);
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle','plugins.generic.hallOfFame.title');
		$templateMgr->assign('userGroups',$userGroups);
		$templateMgr->assign('medalCount',$medalCount);
		$templateMgr->assign('settingMedalCount',$settingMedalCount);
		$templateMgr->assign('userGroups',$userGroups);
		$templateMgr->assign('maxNameLength',$maxNameLength);
		$templateMgr->assign('maxPrizes',$this->getMaxPrizes($medalCount));
		$templateMgr->assign('settingRecency',$settingRecency);
		$templateMgr->assign('percentileRankGold',$settingPercentileRanksArray[0]);
		$templateMgr->assign('percentileRankSilver',$settingPercentileRanksArray[1]);
		$templateMgr->assign('userGroupNames',$userGroupNames);
		$templateMgr->assign('baseUrl',$request->getBaseUrl());	
		$templateMgr->assign('imageDirectory','plugins/generic/hallOfFame/img');	

		$hallOfFamePlugin = PluginRegistry::getPlugin('generic', HALLOFFAME_PLUGIN_NAME);
		$templateMgr->display($hallOfFamePlugin->getTemplatePath()."hallOfFame.tpl");
	}

	function getMaxPrizes(&$medalCount) {
		$maxPrizes = 0;
		$keys = array_keys($medalCount);
		for ($i=0; $i<sizeof($medalCount); $i++) {
			$numberOfPrizes = 0;
			for ($ii=0; $ii<sizeof($this->prizes); $ii++) {
				$numberOfPrizes = $numberOfPrizes + $medalCount[$keys[$i]]['numberOf'.$this->prizes[$ii]];
			}
			$maxPrizes = max($maxPrizes,$numberOfPrizes);
		}
		return $maxPrizes;
	}

	function initializeMedalCount(&$medalCount, $userId, $user, $linkToProfile) {
		for ($i=0; $i<sizeof($this->prizes); $i++) {
			$medalCount[$userId]['numberOf'.$this->prizes[$i]] = 0;
			$medalCount[$userId]['type'][$this->prizes[$i]] = array();
		}
		$medalCount[$userId]['name'] = $user->getFirstName(). " " . $user->getLastName();
		$medalCount[$userId]['linkToProfile'] = $linkToProfile;
	}

	function removeSubmissionsBeforeDate(&$achievements,$cutoffDate) {

		$end = sizeof($achievements);
		$keys = array_keys($achievements);
		for ($i=0; $i<$end; $i++) {
			$submissionId = $achievements[$keys[$i]]['submission_id'];
			// to do strcmp
			$publicationDate = getPublicationDate($submissionId);
			if (!$publicationDate || $publicationDate<$cutoffDate) {
				unset($achievements[$keys[$i]]);
			}
		} 
	}

	function getMaxAchievementUsers($achievements,$date) {

		$this->removeSubmissionsBeforeDate($achievements,$date);
		$keys = array_keys($achievements);
		$numberOfAchievements = array();
		for ($i=0; $i<sizeof($achievements); $i++) {
			$userId = $achievements[$keys[$i]]['user_id'];
			$submissionId = $achievements[$keys[$i]]['submission_id'];
			$publicationDate = getPublicationDate($submissionId);
			if ($publicationDate &&  strcmp($publicationDate,$recentDate)>=0) {
				$numberOfAchievements[$userId]++;
			}
		}

 		$maxAchievements = max($numberOfAchievements);
		$maxAchievementUsers = array();
		$keys = array_keys($numberOfAchievements);
		for ($i=0; $i<sizeof($numberOfAchievements); $i++) {
			if ($numberOfAchievements[$keys[$i]]==$maxAchievements) {

				$maxAchievementUsers[] = $keys[$i];
			}
		}
		$results = array();
		$results['maxAchievements'] = $maxAchievements;
		$results['maxAchievementUsers'] = $maxAchievementUsers;
		return $results;
	}

	function removeNonExistingUsers(&$achievements) {
// to do: muss nicht rückwärts sein (bei allen anderen auch nicht, nur die Schleifen-Werte müssen stabil sein)
		$userDao = DAORegistry::getDAO('UserDAO');
		$keys = array_keys($achievements);
		$start = sizeof($achievements)-1;
		for ($i=$start; $i>=0; $i--) { 
			$userId = $achievements[$keys[$i]]['user_id'];
			if (!$userDao->getById($userId)) {
				unset($achievements[$keys[$i]]);
			}
		}
	}

	function removeAnonymousUsers(&$achievements) {

		$langsciCommonDAO = new LangsciCommonDAO;
		$existsLangSciSettings = $langsciCommonDAO->existsTable("langsci_website_settings");
		if ($existsLangSciSettings) {
			$keys = array_keys($achievements);
		$start = sizeof($achievements)-1;
		for ($i=$start; $i>=0; $i--) { 
				$userId = $achievements[$keys[$i]]['user_id'];
				if (!$langsciCommonDAO->getUserSetting($userId,"HallOfFame")=="true") {
					unset($achievements[$keys[$i]]);
				}
			}
		}
	}

	function getMaxSeriesUsers($achievements) {

		$seriesOfUsers = array();
		$keys = array_keys($achievements);
		for ($i=0; $i<sizeof($achievements);$i++) {
			$item = $achievements[$keys[$i]];
			$seriesId = $this->getSeriesId($item['submission_id']);
			if ($seriesId) {
				$seriesOfUsers[$item['user_id']][$seriesId] = true;
			}
		}

		$keys = array_keys($seriesOfUsers);
		$numberOfSeriesOfUsers = array();
		for ($i=0; $i<sizeof($seriesOfUsers);$i++) {
			$numberOfSeries = sizeof($seriesOfUsers[$keys[$i]]);
			$numberOfSeriesOfUsers[$keys[$i]] = $numberOfSeries;
			$maxSeries = max($maxSeries,$numberOfSeries);
		}
		
		$maxSeries = max($numberOfSeriesOfUsers);
		if ($maxSeries<2) {
			return array();
		}

		$maxUsers = array();
		$keys = array_keys($numberOfSeriesOfUsers);
		for ($i=0; $i<sizeof($numberOfSeriesOfUsers);$i++) {	
			if ($numberOfSeriesOfUsers[$keys[$i]]==$maxSeries) {
				$maxUsers[] = $keys[$i];	
			}
		}
		$results = array();
		$results['maxSeriesUsers'] = $maxUsers;
		$results['maxSeries'] = $maxSeries;
		return $results;
	}

	function getSeriesId($submissionId) {
		$submissionDao = DAORegistry::getDAO('MonographDAO');
		$submission = $submissionDao->getById($submissionId);
		if ($submission) {
			return $submission->getSeriesId();
		}
		return null;
	}



	function getRankPercentiles($achievements) {

		// to do: array_keys

		$rankData = array();
		$keys = array_keys($achievements);
		for ($i=0; $i<sizeof($achievements); $i++) { 
			$userId = $achievements[$keys[$i]]['user_id'];
			$rankData[$userId]['userId'] = $userId;
			$rankData[$userId]['numberOfSubmissions']++;
		}
		usort($rankData,'sort_by_number_of_submissions');

		$numberOfUsers = sizeof($rankData);

		$valueData = array();
		$keys = array_keys($rankData);
		for ($i=0; $i<sizeof($rankData); $i++) { 
			$userId = $keys[$i];
			$value = $rankData[$userId]['numberOfSubmissions'];
			$valueData[$value]['value'] = $value;
			$valueData[$value]['count']++;	
			$rankData[$userId]['rank'] = $i + 1;		
			$valueData[$value]['sum'] += $rankData[$userId]['rank'] ;
		}

		$keys = array_keys($valueData);
		for ($i=0; $i<sizeof($valueData); $i++) {
			$numberOfSubmissions=$keys[$i];
			$valueData[$numberOfSubmissions]['mean'] =
						$valueData[$numberOfSubmissions]['sum'] / $valueData[$numberOfSubmissions]['count']; 
			$valueData[$numberOfSubmissions]['rankPercentile'] = $valueData[$numberOfSubmissions]['mean']/ $numberOfUsers*100; 
		}	

		$rankPercentiles = array();
		$keys = array_keys($valueData);
		for ($i=0; $i<sizeof($valueData); $i++) {
			$numberOfSubmissions=$keys[$i];
			$rankPercentiles[$numberOfSubmissions] = $valueData[$numberOfSubmissions]['rankPercentile'];
		}			
		return $rankPercentiles;
	}

	function getNumberOfAchievements($achievements) {
		
		$numberOfAchievements = array();
		$keys = array_keys($achievements);
		for ($i=0; $i<sizeof($achievements); $i++) { 
			$numberOfAchievements[$achievements[$keys[$i]]['user_id']]++;
		}
		return $numberOfAchievements;
	}

	function getMedalCountRanks(&$medalCount) {
		$keys = array_keys($medalCount);
		$rank = 0;
		$rankSave = 0;
		
		for ($i=0; $i<sizeof($medalCount); $i++) {

			$achievements2 = array($medalCount[$keys[$i]]['numberOfgold'],
								   $medalCount[$keys[$i]]['numberOfsilver'],
								   $medalCount[$keys[$i]]['numberOfbronze'],
								   $medalCount[$keys[$i]]['numberOfseries'],
								   $medalCount[$keys[$i]]['numberOfrecent']
							);

			if ($i==0) {
				$better = true;
			} else {
				$better = false;
				for ($ii=0; $ii<sizeof($achievements1); $ii++) {
					if ($achievements1[$ii]>$achievements2[$ii]) {
						$better = true;
						$rank = $rank + $rankSave;
						$rankSave = 0;
						break;
					}
				}
			}

			if ($better) {
				$rank++;
			} else {
				$rankSave++;
			}
			$medalCount[$keys[$i]]['rank'] = $rank;
			$achievements1 = $achievements2;
		}
	}

}

// sort functions

function sort_users($a, $b) {

	if ($a['numberOfSubmissions']==$b['numberOfSubmissions']) {
		return  strcasecmp($a['lastName'],$b['lastName']);
	} else {
		return  $b['numberOfSubmissions'] - $a['numberOfSubmissions'];
	}
}

function sort_by_number_of_submissions($a, $b) {
	return  $b['numberOfSubmissions'] - $a['numberOfSubmissions'];
}

function sort_for_medal_count($a, $b) {

	if ($a['numberOfgold']!= $b['numberOfgold']) {
		return $b['numberOfgold']-$a['numberOfgold'];
	} elseif ($a['numberOfsilver']!= $b['numberOfsilver']) {
		return $b['numberOfsilver']-$a['numberOfsilver'];
	} elseif ($a['numberOfbronze']!= $b['numberOfbronze']) {
		return $b['numberOfbronze']-$a['numberOfbronze'];
	} elseif ($a['numberOfseries']!= $b['numberOfseries']) {
		return $b['numberOfseries']-$a['numberOfseries'];
	} elseif ($a['numberOfrecent']!= $b['numberOfrecent']) {
		return $b['numberOfrecent']-$a['numberOfrecent'];
	} else {
		return 0;
	}
}

?>
