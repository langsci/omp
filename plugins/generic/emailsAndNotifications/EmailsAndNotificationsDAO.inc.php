<?php

/**
 * @file plugins/generic/emailsAndNotifications/EmailsAndNotificationsDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.  
 *
 * @class EmailsAndNotificationsDAO
 *
 */

class EmailsAndNotificationsDAO extends DAO {
	/**
	 * Constructor
	 */
	function EmailsAndNotificationsDAO() {
		parent::DAO();
	}


	function setSubject($subject) {

		$result = $this->update(
			'update email_templates_data set subject= "'.$subject.'" where email_key="NOTIFICATION"');

	}

	
}

?>
