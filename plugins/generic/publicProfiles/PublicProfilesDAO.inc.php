<?php

/**
 * @file PublicProfilesDAO.inc.php
 *
 * Copyright (c) 2000-2015 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PublicProfilesDAO
 *
 * Class for Public Profiles DAO.
 */

class PublicProfilesDAO extends DAO {
	/**
	 * Constructor
	 */
	function PublicProfilesDAO() {
		parent::DAO();
	}

	function existsTable($table) {

		$result = $this->retrieve(
			"SHOW TABLES LIKE '".$table."'"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();
			return true;
		}
	}

	function setUserSetting($user_id,$setting_name,$insert) {

		$result = $this->update(
			"DELETE FROM langsci_website_settings
				WHERE setting_name='".$setting_name."' AND user_id =" . $user_id			
		);

		if ($insert) {
			$result = $this->update(
				"INSERT INTO langsci_website_settings VALUES(".$user_id.",'".$setting_name."','true')");
		} 
	}

	function getUserSetting($user_id,$setting_name) {

		$result = $this->retrieve(
			"SELECT setting_value FROM langsci_website_settings
				WHERE setting_name='".$setting_name."' AND user_id =" . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['setting_value']);
		}
	}



	function showProfile($user_id) {

		$result = $this->retrieve(
			"SELECT user_id FROM langsci_user_websitesettings
				WHERE setting_value='public profile' AND user_id =" . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();			
			return true;
		}
	}

	function showEmail($user_id) {

		$result = $this->retrieve(
			"SELECT user_id FROM langsci_user_websitesettings
				WHERE setting_value='show email' AND user_id =" . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();			
			return true;
		}
	}

	function getUserData($user_id) {

		$result_users = $this->retrieve(
			"SELECT username, first_name, last_name, url, email, salutation FROM users WHERE user_id=".$user_id
		);

		$result_affiliation = $this->retrieve(
			"SELECT setting_value FROM user_settings WHERE locale = 'en_US' AND setting_name='affiliation' AND user_id=".$user_id
		);

		$result_biostatement = $this->retrieve(
			"SELECT setting_value FROM user_settings WHERE locale = 'en_US' AND setting_name='biography' AND user_id=".$user_id
		);

		$userData = array();

		if (!$result_users->RecordCount() == 0) {
			$row = $result_users->getRowAssoc(false);
			$userData[0] = $this->convertFromDB($row['username']);
			$userData[1] = $this->convertFromDB($row['first_name']);
			$userData[2] = $this->convertFromDB($row['last_name']);
			$userData[3] = $this->convertFromDB($row['url']);
			$userData[4] = $this->convertFromDB($row['email']);
			$userData[5] = $this->convertFromDB($row['salutation']);
		}

		if (!$result_affiliation->RecordCount() == 0) {
			$row = $result_affiliation->getRowAssoc(false);
			$userData[6] = $this->convertFromDB($row['setting_value']);
		}
		if (!$result_biostatement->RecordCount() == 0) {
			$row = $result_biostatement->getRowAssoc(false);
			$userData[7] = $this->convertFromDB($row['setting_value']);
		}

		$result_users->Close();
		$result_affiliation->Close();
		$result_biostatement->Close();

		return $userData;
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


	function getSubmissionsFromStageAssignments($user_id, $user_group_id,$onlyPublishedSubmissions) {

		if ($onlyPublishedSubmissions) {
			$result = $this->retrieve(
				"SELECT submission_id FROM stage_assignments WHERE
				user_id = " . $user_id . " AND
				user_group_id=" . $user_group_id . " AND
				submission_id IN (SELECT submission_id FROM published_submissions WHERE date_published IS NOT NULL)"
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


// to do: hardwired 7 and 8 raus, 5

	function getNumberOfTypesettings($user_id) {

		$result = $this->retrieve(
			'select count(*) from stage_assignments where user_group_id=7 and user_id=' . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return 0;
		} else {
			$row = $result->getRowAssoc(false);
			return $this->convertFromDB($row['count(*)']);		
		}
	}

	function getNumberOfProofreadings($user_id) {

		$result = $this->retrieve(
			'select count(*) from stage_assignments where user_group_id=8 and user_id=' . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return 0;
		} else {
			$row = $result->getRowAssoc(false);
			return $this->convertFromDB($row['count(*)']);		
		}
	}

	function getNumberOfAuthoredBooks($user_id) {

		$result = $this->retrieve(
			'select count(*) from stage_assignments where user_group_id=5 and user_id=' . $user_id			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return 0;
		} else {
			$row = $result->getRowAssoc(false);
			return $this->convertFromDB($row['count(*)']);		
		}
	}


}

?>
