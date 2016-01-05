
<?php

/**
 * @file plugins/generic/publicProfiles/LangsciCommonDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LangsciCommonDAO
 *
 */

class LangsciCommonDAO extends DAO {
	/**
	 * Constructor
	 */
	function LangsciCommonDAO() {
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


}

?>

