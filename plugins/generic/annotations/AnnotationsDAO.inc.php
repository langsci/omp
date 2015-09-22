<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameDAO.inc.php
 *
 * Copyright (c) 2000-2015 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameDAO
 *
 */

class AnnotationsDAO extends DAO {
	/**
	 * Constructor
	 */
	function AnnotationsDAO() {
		parent::DAO();
	}


	function getFileIds() {

		$result = $this->retrieve(
			'SELECT submission_id, file_id FROM submission_files WHERE viewable=1 AND assoc_id IN
			(SELECT publication_format_id FROM publication_format_settings WHERE setting_value="Open Review")');

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

	function getPublicationFormatIds($fileIds) {

		$result = $this->retrieve(
			'SELECT assoc_id FROM submission_files WHERE file_id IN ('.$fileIds.');');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$publicationFormatIds = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$publicationFormatIds[] = $this->convertFromDB($row['assoc_id']);
				$result->MoveNext();
			}
			$result->Close();
			return $publicationFormatIds;
		}	

	}

	function getTitle($submissionId) {

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


	function getPressIds($submissionIds) {

		$result = $this->retrieve(
			'select context_id from submissions where submission_id in ('.$submissionIds.');');

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$pressIds = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$pressIds[] = $this->convertFromDB($row['context_id']);
				$result->MoveNext();
			}
			$result->Close();
			return $pressIds;
		}	

	}





}


/*

	function getUserSetting($user_id,$setting_name) {

		$result = $this->retrieve(
			"SELECT setting_value FROM langsci_website_settings WHERE
				user_id=".$user_id." AND
				setting_name='".$setting_name."'");
			
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

	function getNameOfUser($user_id) {

		$result = $this->retrieve(
			'SELECT first_name, last_name FROM users WHERE user_id=' . $user_id
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$user = "";
			$row = $result->getRowAssoc(false);
			$users .= $this->convertFromDB($row['first_name']);
			$users .= " " . $this->convertFromDB($row['last_name']);
			$result->Close();
			return $users;	
		}
	}

	function getUserRanking($user_group_id,$onlyPublishedSubmissions) {

		if ($onlyPublishedSubmissions) {

				$result = $this->retrieve(
				'SELECT COUNT(*) AS number_of_entries, user_id from stage_assignments WHERE
				user_group_id = '.$user_group_id.' AND
				submission_id IN (SELECT submission_id FROM published_submissions WHERE date_published IS NOT NULL) 
				GROUP BY user_id
				ORDER BY number_of_entries DESC,user_id'
			);

		} else {

			$result = $this->retrieve(
				'SELECT COUNT(*) AS number_of_entries, user_id from stage_assignments WHERE
				user_group_id = '.$user_group_id.' AND
				submission_id IN (select submission_id from submission_settings where setting_name="prefix" and setting_value not like "%Forthcoming%" and locale="en_US" and submission_id IN (select submission_id from published_submissions WHERE date_published IS NOT NULL)) 
				GROUP BY user_id
				ORDER BY number_of_entries DESC,user_id'
			);
		}

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$users = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$users[$rownr][0] = $this->convertFromDB($row['number_of_entries']);
				$users[$rownr][1] = $this->convertFromDB($row['user_id']);
				$rownr = $rownr + 1;
				$result->MoveNext();
			}

			$result->Close();
			return $users;
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
				'SELECT submission_id FROM stage_assignments WHERE
				user_id = ' . $user_id . ' AND
				user_group_id=' . $user_group_id . ' AND
				submission_id IN (select submission_id from submission_settings where setting_name="prefix" and setting_value not like "%Forthcoming%" and locale="en_US" and submission_id IN (select submission_id from published_submissions WHERE date_published IS NOT NULL))'
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
	// get user_group_id by user_group (e.g. Typesetting, Proofreader, ...)


	// get user_group_id by user_group (e.g. Typesetting, Proofreader, ...)
	function getUserGroupId($user_group) {

		$result = $this->retrieve(
			"SELECT user_group_id FROM user_group_settings WHERE setting_name='name' AND locale='en_US' AND setting_value='".$user_group."'"				
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
}*/

?>
