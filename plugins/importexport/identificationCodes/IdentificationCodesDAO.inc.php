<?php

/**
 * @file plugins/generic/identificationCodes/IdentificationCodesDAO.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.  
 *
 * @class IdentificationCodesDAO
 *
 */

class IdentificationCodesDAO extends DAO {
	/**
	 * Constructor
	 */
	function IdentificationCodesDAO() {
		parent::DAO();
	}

	function submissionExists($submissionId) {

		$result = $this->retrieve('select submission_id from submissions where submission_id='.$submissionId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();
			return true;
		}



	}

	function getPublicationFormatIds($submissionId,$publicationFormat) {

		$result = $this->retrieve('select pf.publication_format_id as pfid from publication_formats as pf left join publication_format_settings as pfs on pfs.publication_format_id=pf.publication_format_id where pfs.setting_name="name" and pfs.setting_value="'.$publicationFormat.'" and pf.submission_id='.$submissionId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$publicationFormatIds = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$publicationFormatIds[] = $this->convertFromDB($row['pfid']);
				$result->MoveNext();
			}
			$result->Close();
			return $publicationFormatIds;
		}

	}

	function getCodeValue($publicationFormatId,$codeId) {

		$result = $this->retrieve('select value from identification_codes where publication_format_id='.$publicationFormatId.' and code='.$codeId);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['value']);;
		}
	}

	function insertCode($publicationFormatId,$codeId, $codeValue) {
		$this->update('insert into identification_codes(publication_format_id, code, value) values('.$publicationFormatId.','.$codeId.',"'.$codeValue.'")');
		return true;
	}

	function updateCodeValue($publicationFormatId,$codeId, $codeValue) {

		$this->update('update identification_codes set value="'.$codeValue.'" where code='.$codeId.' and publication_format_id='.$publicationFormatId);
		return true;
	}

	function getData($locale) {

		$result = $this->retrieve("SELECT pf.publication_format_id, pfs.setting_value AS pfname, ic.code, ic.value, pf.submission_id,ss.setting_value AS btitle FROM identification_codes ic LEFT JOIN publication_formats pf ON pf.publication_format_id=ic.publication_format_id
LEFT JOIN publication_format_settings pfs ON pf.publication_format_id=pfs.publication_format_id 
LEFT JOIN submission_settings ss ON ss.submission_id=pf.submission_id WHERE pfs.setting_name='name' and ss.setting_name='title'  AND ss.locale='".$locale."' and pfs.locale='".$locale."' ORDER BY pf.submission_id"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {

			$identificationCodes = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);

				$publicationFormatId = $this->convertFromDB($row['publication_format_id']);
				$codeId = $this->convertFromDB($row['code']); 

				$identificationCodes[$publicationFormatId]['subId'] = $this->convertFromDB($row['submission_id']);
				$identificationCodes[$publicationFormatId]['title'] = $this->convertFromDB($row['btitle']); 
				$identificationCodes[$publicationFormatId]['publicationFormat'] = $this->convertFromDB($row['pfname']);
				$identificationCodes[$publicationFormatId][$codeId] = $this->convertFromDB($row['value']);
				$result->MoveNext();
			}
			$result->Close();
			return $identificationCodes;
		}
	}

}

?>
