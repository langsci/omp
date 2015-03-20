<?php

/**
 * @file plugins/generic/simplifyWorkflow/SimplifyWorkflowDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SimplifyWorkflowDAO
 *
 */

class SimplifyWorkflowDAO extends DAO {
	/**
	 * Constructor
	 */
	function SimplifyWorkflowDAO() {
		parent::DAO();
	}

	function setTermsToOpenAcess(){
		$this->update("UPDATE submission_files SET sales_type='openAccess'");
		$this->update("UPDATE submission_files SET direct_sales_price=0");
	}

	function getSeriesEditors($submission_id) {

		$result = $this->retrieve(
			'select user_id from series_editors
			 where series_id = (select series_id from submissions
			 where submission_id='.$submission_id.');'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$users = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$users[$rownr] = $this->convertFromDB($row['user_id']);
				$rownr = $rownr + 1;				 
				$result->MoveNext();
			}
			$result->Close();
			return $users;
		}
	}

	function getPressManagers() {

		$result = $this->retrieve(
			'select user_id from user_user_groups
			 where user_group_id = (select user_group_id from user_group_settings
			 where setting_value="Press Manager" and locale="en_US")'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$users = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$users[$rownr] = $this->convertFromDB($row['user_id']);
				$rownr = $rownr + 1;				 
				$result->MoveNext();
			}
			$result->Close();
			return $users;
		}
	}

	function getRoleId($roleName) {

		$result = $this->retrieve(
			'select user_group_id from user_group_settings where setting_value = "'.$roleName.'" and locale="en_US"'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$userGroupId = $this->convertFromDB($row['user_group_id']);				 
			$result->Close();
			return $userGroupId;
		}
	}

	function assignParticipant($submission_id,$user_group_id,$user_id) {

		$this->update('insert into stage_assignments (submission_id, user_group_id, user_id, date_assigned)
				values('.$submission_id.','.$user_group_id.','.$user_id.',NOW())'
		);
	}

	function deleteNotificationAssignEditor() {

		$this->update('delete from notifications where type=16777247');
	}

	// add publication format: PDF, digital, physical_format
	function addStandardValuesAfterSubmit($submission_id) {

		// add PDF and BibTeX
		$publication_format_id = NULL;

		$this->update('INSERT INTO publication_formats(submission_id, physical_format, entry_key,
						product_composition_code,is_available,imprint)
						VALUES('.$submission_id.',0, "DA","00",1,"Language Science Press")');
		$this->update('INSERT INTO publication_formats(submission_id, physical_format, entry_key,
						product_composition_code,is_available,imprint)
						VALUES('.$submission_id.',0, "DA","00",1,"Language Sciece Press")');

		$result = $this->retrieve(
			'SELECT publication_format_id FROM publication_formats
			 WHERE submission_id = '.$submission_id
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$publication_format_id_pdf = $this->convertFromDB($row['publication_format_id']);
			$result->MoveNext();
			$row = $result->getRowAssoc(false);
			$publication_format_id_bib = $this->convertFromDB($row['publication_format_id']);
		}
	
		$this->update("INSERT INTO publication_format_settings
				VALUES(".$publication_format_id_pdf.",'en_US','name','PDF','string')");
		$this->update("INSERT INTO publication_format_settings
				VALUES(".$publication_format_id_bib.",'en_US','name','BibTeX','string')");

	}

}

?>
