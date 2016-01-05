<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameDAO.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameDAO
 *
 */

class HallOfFameDAO extends DAO {

	function HallOfFameDAO() {
		parent::DAO();
	}

	// get all user-submission tuples for one user group, only get those submissions that are in the catalog (date_published not null)		
	function getAchievements($userGroup) {

		$result = $this->retrieve('select user_id,submission_id from stage_assignments where user_group_id='.$userGroup.' and submission_id IN (select submission_id from published_submissions WHERE date_published IS NOT NULL)');
		
		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$i=0;
			$submissions = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$submissions[$i]['user_id'] = $this->convertFromDB($row['user_id']);
				$submissions[$i]['submission_id'] = $this->convertFromDB($row['submission_id']);
				$i++;
				$result->MoveNext();
			}
			$result->Close();
			return $submissions;
		}	
	}

}

?>
