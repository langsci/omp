<?php

/**
 * @file plugins/generic/userWebsiteSettings/UserWebsiteSettingsDAO.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserWebsiteSettingsDAO
 *
 */

class UserWebsiteSettingsDAO extends DAO {
	/**
	 * Constructor
	 */
	function UserWebsiteSettingsDAO() {
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

	function setWebsiteSetting($user_id,$setting_name,$insert) {

		$result = $this->update(
			"DELETE FROM langsci_website_settings
				WHERE setting_name='".$setting_name."' AND user_id =" . $user_id			
		);

		if ($insert) {
			$result = $this->update(
				"INSERT INTO langsci_website_settings VALUES(".$user_id.",'".$setting_name."','true')");
		} 
	}

	function getWebsiteSetting($user_id,$setting_name) {

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
	
}

?>
