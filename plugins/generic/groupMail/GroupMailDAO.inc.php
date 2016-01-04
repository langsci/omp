<?php

/**
 * @file plugins/generic/groupMail/GroupMailDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.  
 *
 * @class GroupMailDAO
 *
 */

class GroupMailDAO extends DAO {
	/**
	 * Constructor
	 */
	function GroupMailDAO() {
		parent::DAO();
	}

	function getUserGroups() {

		$result = $this->retrieve(
		'SELECT user_group_id,setting_value FROM user_group_settings WHERE locale="en_US" AND setting_name="name";'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$userGroups = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$userGroups[$this->convertFromDB($row['user_group_id'])] = $this->convertFromDB($row['setting_value']); 
				$result->MoveNext();
			}
			$result->Close();
			return $userGroups;
		}
	}

	function getEmailsByGroup($query) {

		$result = $this->retrieve($query);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$emails = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$emails[$this->convertFromDB($row['email'])] = $this->convertFromDB($row['first_name']) . " " . $this->convertFromDB($row['last_name']);		 
				$result->MoveNext();
			}
			$result->Close();
			return $emails;
		}
	}

}

?>
