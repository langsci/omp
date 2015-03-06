<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameDAO
 *
 */

class HallOfFameDAO extends DAO {
	/**
	 * Constructor
	 */
	function HallOfFameDAO() {
		parent::DAO();
	}

	function existsLangSciSettings() {

		$result = $this->retrieve(
			"SHOW TABLES LIKE 'langsci_user_websitesettings'"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();
			return true;
		}
	}

	function getUserSetting($user_id,$setting_name) {

		$result = $this->retrieve(
			"SELECT setting_value FROM langsci_website_settings WHERE
				user_id=".$user_id." AND
				setting_name='".$setting_name."'");
			
		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['setting_value']);
		}	
	}

	function getUserGroupIdByName($user_group_name) {
		$result = $this->retrieve(
			"SELECT user_group_id FROM user_group_settings WHERE
				locale='en_US' AND
				setting_name = 'name' AND
				setting_value = '" . $user_group_name . "'"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['user_group_id']);
		}	

	}	

	function getNameOfUser($user_id) {

		$result = $this->retrieve(
			'SELECT first_name, last_name FROM users WHERE user_id=' . $user_id
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$user = "";
			$row = $result->getRowAssoc(false);
			$users .= $this->convertFromDB($row['first_name']);
			$users .= " " . $this->convertFromDB($row['last_name']);
			$result->Close();
			return $users;	
		}
	}

	function getUserRanking($user_group_id,$onlyPublishedSubmissions) {

		if ($onlyPublishedSubmissions) {
			$result = $this->retrieve(
				'SELECT COUNT(*) AS number_of_entries, user_id from stage_assignments WHERE
				user_group_id = '.$user_group_id.' AND
				submission_id IN (SELECT submission_id FROM published_submissions) 
				GROUP BY user_id
				ORDER BY number_of_entries DESC,user_id'
			);
		} else {
			$result = $this->retrieve(
				'SELECT COUNT(*) AS number_of_entries, user_id from stage_assignments WHERE
				user_group_id = '.$user_group_id.'
				GROUP BY user_id
				ORDER BY number_of_entries DESC,user_id'
			);
		}

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$users = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$users[$rownr][0] = $this->convertFromDB($row['number_of_entries']);
				$users[$rownr][1] = $this->convertFromDB($row['user_id']);
				$rownr = $rownr + 1;
				$result->MoveNext();
			}

			$result->Close();
			return $users;
		}		
	}
	
	function getSubmissionsFromStageAssignments($user_id, $user_group_id,$onlyPublishedSubmissions) {

		if ($onlyPublishedSubmissions) {
			$result = $this->retrieve(
				"SELECT submission_id FROM stage_assignments WHERE
				user_id = " . $user_id . " AND
				user_group_id=" . $user_group_id . " AND
				submission_id IN (SELECT submission_id FROM published_submissions)"
			);

		} else {
			$result = $this->retrieve(
				"SELECT submission_id FROM stage_assignments WHERE
				user_id = ".$user_id." AND
				user_group_id=" . $user_group_id
			);
		}
		
		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$submissions = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$submissions[] = $this->convertFromDB($row['submission_id']);
				$result->MoveNext();
			}
			$result->Close();
			return $submissions;
		}	
	}

	// get user_group_id by user_group (e.g. Typesetting, Proofreader, ...)
	function getUserGroupId($user_group) {

		$result = $this->retrieve(
			"SELECT user_group_id FROM user_group_settings WHERE setting_name='name' AND locale='en_US' AND setting_value='".$user_group."'"				
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['user_group_id']);
		}				
	}
	
}

?>
