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

class IdentificationCodesDAO extends DAO {
	/**
	 * Constructor
	 */
	function IdentificationCodesDAO() {
		parent::DAO();
	}

	function getData() {

		$result = $this->retrieve("select ic.publication_format_id, ic.code, ic.value, pf.submission_id,ss.setting_value from identification_codes ic left join publication_formats pf on pf.publication_format_id=ic.publication_format_id left join submission_settings ss on ss.submission_id=pf.submission_id where ss.setting_name='title' and ss.locale='en_US'"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {

			$identificationCodes = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);

				$subId = $this->convertFromDB($row['submission_id']);
				$codeId = $this->convertFromDB($row['code']); 

				$identificationCodes[$subId]['subId'] = $subId; 
				$identificationCodes[$subId]['title'] = $this->convertFromDB($row['setting_value']); 
				$identificationCodes[$subId][$codeId] = $this->convertFromDB($row['value']);
				$result->MoveNext();
			}
			$result->Close();
			return $identificationCodes;
		}
	}

}

?>
