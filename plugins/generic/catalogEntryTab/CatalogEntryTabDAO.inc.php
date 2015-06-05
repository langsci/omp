<?php

/**
 * @file plugins/generic/seriesPage/SeriesPAgeDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesPageDAO
 *

create table langsci_submission_links (
submission_id int(6),
softcover tinyint(1),
link varchar(30)
)

 */

class CatalogEntryTabDAO extends DAO {
	/**
	 * Constructor
	 */
	function CatalogEntryTabDAO() {
		parent::DAO();
	}

	function getLink($submission_id,$softcover) {

		$result = $this->retrieve(
			'SELECT link FROM langsci_submission_links WHERE submission_id='.$submission_id.' AND softcover='.$softcover
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

	function setLink($submission_id,$softcover,$link) {

		$this->update(
			'DELETE FROM langsci_submission_links WHERE submission_id= '.$submission_id.' AND softcover='.$softcover
		);

		$this->update(
			'INSERT INTO langsci_submission_links VALUES('.$submission_id.','.$softcover.',"'.$link.'")'
		);

	}

}

?>





