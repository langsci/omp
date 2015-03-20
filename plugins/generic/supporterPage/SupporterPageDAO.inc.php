<?php

/**
 * @file plugins/generic/supporterPage/SupporterPageDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SupporterPageDAO
 *
 * Class for Supporter Page DAO.
 */

class SupporterPageDAO extends DAO {
	/**
	 * Constructor
	 */
	function SupporterPageDAO() {
		parent::DAO();
	}

	function getSupporterNames() {

		$result = $this->retrieve('SELECT user_id, first_name, last_name FROM users WHERE user_id IN
											(SELECT user_id FROM user_user_groups WHERE user_group_id=
											(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Supporter")) order by date_registered');


		if ($result->RecordCount() == 0) {
			$result->Close();
			return $null;
		} else {
			$supporterNames = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$key = $this->convertFromDB($row['user_id']);
				$supporterNames[$key] = $this->convertFromDB($row['first_name'])." ".$this->convertFromDB($row['last_name']); 			 
				$result->MoveNext();
			}
			$result->Close();
			return $supporterNames;
		}
	}

	function getSupporterUrl() {

		$result = $this->retrieve('SELECT user_id, url FROM users WHERE user_id IN
											(SELECT user_id FROM user_user_groups WHERE user_group_id=
											(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Supporter"))');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return $null;
		} else {
			$url = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$key = $this->convertFromDB($row['user_id']);	
				$url[$key] = $this->convertFromDB($row['url']); 			 
				$result->MoveNext();
			}
			$result->Close();
			return $url;
		}
	}

	function getSupporterProminent() {

		$result = $this->retrieve('SELECT user_id, prominent FROM langsci_user_prominent WHERE user_id IN
											(SELECT user_id FROM user_user_groups WHERE user_group_id=
											(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Supporter"))');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return $null;
		} else {
			$prominent = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$key = $this->convertFromDB($row['user_id']);
				$prominent[$key] = $this->convertFromDB($row['prominent']);				 
				$result->MoveNext();
			}
			$result->Close();
			return $prominent;
		}
	}

	function getSupporterAcademics() {

		$result = $this->retrieve('SELECT user_id, academic_title FROM langsci_user_academic WHERE user_id IN
											(SELECT user_id FROM user_user_groups WHERE user_group_id=
											(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Supporter"))');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return $null;
		} else {
			$academics = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$key = $this->convertFromDB($row['user_id']);
				$academics[$key] = $this->convertFromDB($row['academic_title']);			 
				$result->MoveNext();
			}
			$result->Close();
			return $academics;
		}
	}

	function getSupporterAffiliation() {

		$result = $this->retrieve('SELECT user_id,setting_value FROM user_settings WHERE setting_name="affiliation" AND locale="en_US" AND user_id IN
											(SELECT user_id FROM user_user_groups WHERE user_group_id=
											(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Supporter"))');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return $null;
		} else {
			$affilication = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$key = $this->convertFromDB($row['user_id']);
				$affilication[$key] = $this->convertFromDB($row['setting_value']);			 
				$result->MoveNext();
			}
			$result->Close();
			return $affilication;
		}
	}




}

?>
