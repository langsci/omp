<?php

/**
 * @file plugins/generic/seriesPage/SeriesPAgeDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesPageDAO
 *
 */

class SeriesPageDAO extends DAO {
	/**
	 * Constructor
	 */
	function SeriesPageDAO() {
		parent::DAO();
	}

	function getSeries() {

		$result = $this->retrieve(
		'select series.series_id, series.path,series_settings.setting_value from series, series_settings where series_settings.setting_name="title" and series_settings.locale="en_US" and series.series_id=series_settings.series_id order by series_settings.setting_value;'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$rownr=0;
			$series = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$series[$rownr][0] = $this->convertFromDB($row['path']);
				$series[$rownr][1] = $this->convertFromDB($row['setting_value']);
				$series[$rownr][2] = $this->convertFromDB($row['series_id']);
				$rownr = $rownr + 1;				 
				$result->MoveNext();
			}
			$result->Close();
			return $series;
		}
	}

	function getSubmissionIds() {

		$result = $this->retrieve(
		'select series_id, submission_id from submissions group by series_id'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$submissionIds = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$seriesId = $this->convertFromDB($row['series_id']);
				$submissionIds[$seriesId] = $this->convertFromDB($row['submission_id']);		 
				$result->MoveNext();
			}
			$result->Close();
			return $submissionIds;
		}
	}






}

?>
