<?php

/**
 * @file plugins/generic/simplifyWorkflow/SimplifyWorkflowDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SimplifyWorkflowDAO
 *
 */

error_reporting(E_ALL);
ini_set('display_errors', true);

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
			'SELECT user_id FROM series_editors
			 WHERE press_id=1 AND
			 series_id = (SELECT series_id FROM submissions
			 WHERE submission_id='.$submission_id.');'
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

		// only assign participant if he/she is not already assigned
		$result = $this->retrieve(
			"SELECT  * FROM stage_assignments WHERE submission_id=".$submission_id." AND user_group_id=".$user_group_id." AND user_id=".$user_id
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			$this->update('insert into stage_assignments (submission_id, user_group_id, user_id, date_assigned)
				values('.$submission_id.','.$user_group_id.','.$user_id.',NOW())'
			);
			return true;
		} else {
			return false;
		}
	}

	function deleteNotificationAssignEditor() {

		$this->update('delete from notifications where type=16777247');
	}

	// add publication format: PDF, digital, physical_format
	// add PDF and BibTeX and 5 Chapter for Edited Volumes
	function addStandardValuesAfterSubmit($submission_id) {

		// is the submission an edited volume?
		$edvolume = $this->retrieve(
			'SELECT edited_volume from submissions
			 WHERE submission_id = '.$submission_id
		);
 	
		$editedVolume = 2;
		if ($edvolume->RecordCount() == 0) {
			$edvolume->Close();
			return null;
		} else {
			$row = $edvolume->getRowAssoc(false);
			$editedVolume = $this->convertFromDB($row['edited_volume']);
			$edvolume->Close();
		}

		// insert publication formats for the submission
		$numberOfPubFormats = 2;
		if ($editedVolume==1) {
			$numberOfPubFormats = 7;
		}
		for ($i=0; $i<$numberOfPubFormats; $i++) {
			$this->update('INSERT INTO publication_formats(submission_id, physical_format, entry_key,
						   product_composition_code,is_available,imprint)
						   VALUES('.$submission_id.',0, "DA","00",1,"Language Sciece Press")');			
		}

		// get publication format ids
		$pfids = $this->retrieve(
			'SELECT publication_format_id FROM publication_formats
			 WHERE submission_id = '.$submission_id
		);

		$pubFormatIds = array();
		if ($pfids->RecordCount() == 0) {
			$pfids->Close();
			return null;
		} else {
			while (!$pfids->EOF) {
				$row = $pfids->getRowAssoc(false);
				$pubFormatIds[] = $this->convertFromDB($row['publication_format_id']);
				$pfids->MoveNext();
			}
			$pfids->Close();
		}

		// add names to the publication formats
		$this->update("INSERT INTO publication_format_settings
				VALUES(".$pubFormatIds[0].",'en_US','name','Complete book','string')");
		$this->update("INSERT INTO publication_format_settings
				VALUES(".$pubFormatIds[1].",'en_US','name','Bibliography','string')");
		if ($editedVolume==1) {
			$this->update("INSERT INTO publication_format_settings
					VALUES(".$pubFormatIds[2].",'en_US','name','Chapter 1','string')");
			$this->update("INSERT INTO publication_format_settings
					VALUES(".$pubFormatIds[3].",'en_US','name','Chapter 2','string')");
			$this->update("INSERT INTO publication_format_settings
					VALUES(".$pubFormatIds[4].",'en_US','name','Chapter 3','string')");
			$this->update("INSERT INTO publication_format_settings
					VALUES(".$pubFormatIds[5].",'en_US','name','Chapter 4','string')");
			$this->update("INSERT INTO publication_format_settings
					VALUES(".$pubFormatIds[6].",'en_US','name','Chapter 5','string')");
		}

	}

}

?>
