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

class CatalogEntryTabDAO extends DAO {
	/**
	 * Constructor
	 */
	function CatalogEntryTabDAO() {
		parent::DAO();
	}

	function getLink($submission_id,$link_name) {

		$result = $this->retrieve(
			'SELECT link FROM langsci_submission_links WHERE submission_id='.$submission_id.' AND link_name="'.$link_name.'"'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$link = $this->convertFromDB($row['link']);				 
			$result->Close();
			return $link;
		}
	}

	function setLink($submission_id,$link_name,$link) {

		$this->update(
			'DELETE FROM langsci_submission_links WHERE submission_id= '.$submission_id.' AND link_name="'.$link_name.'"'
		);

		$this->update(
			'INSERT INTO langsci_submission_links VALUES('.$submission_id.',"'.$link_name.'","'.$link.'")'
		);

	}

}

?>
