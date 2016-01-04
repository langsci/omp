<?php

/**
 * @file plugins/generic/annotations/AnnotationsDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AnnotationsDAO
 *
 */

class AnnotationsDAO extends DAO {

	function AnnotationsDAO() {
		parent::DAO();
	}

	function getFileIds($viewable) {

		$result = $this->retrieve(
			'SELECT submission_id, file_id FROM submission_files WHERE viewable='.$viewable.' AND assoc_id IN
			(SELECT publication_format_id FROM publication_format_settings WHERE setting_value="Open Review" OR setting_value="Proofreading")');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$ids = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$ids[$this->convertFromDB($row['file_id'])] = $this->convertFromDB($row['submission_id']);
				$rownr = $rownr + 1;
				$result->MoveNext();
			}
			$result->Close();
			return $ids;
		}	
	}

	function getPublicationFormatId($fileId) {

		$result = $this->retrieve(
			'SELECT assoc_id FROM submission_files WHERE file_id='.$fileId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['assoc_id']);
		}	
	}

	function getSubTitle($submissionId) {

		$result = $this->retrieve(
			'select setting_value from submission_settings where setting_name="title" and locale="en_US" and
				submission_id ='.$submissionId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['setting_value']);
		}	

	}

	function getFileName($fileId) {

		$result = $this->retrieve(
			'select setting_value from submission_file_settings where setting_name="name" and locale="en_US" and file_id='.$fileId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['setting_value']);
		}	

	}

	function getPressId($submissionId) {

		$result = $this->retrieve(
			'select context_id from submissions where submission_id='.$submissionId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['context_id']);
		}	
	}

	function getUserRoles($userId) {

		$result = $this->retrieve(
			'select setting_value from user_group_settings where setting_name = "name" and locale="en_US" and
			 user_group_id in (select user_group_id from user_user_groups where user_id = '.$userId.')');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$userGroups = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$userGroups[] = $this->convertFromDB($row['setting_value']);
				$result->MoveNext();
			}
			$result->Close();
			return $userGroups;
		}	
	}

}


?>
