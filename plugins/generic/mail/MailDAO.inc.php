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

class MailDAO extends DAO {
	/**
	 * Constructor
	 */
	function MailDAO() {
		parent::DAO();
	}


	function setSubject($subject) {

		$result = $this->update(
			'update email_templates_data set subject= "'.$subject.'" where email_key="NOTIFICATION"');

	}

	
}

?>
