<?php

/**
 * @file plugins/generic/registerPage/includes/getSubscriptionData.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * short code to get input data from form
 */

		$area1 = $_POST['selectArea1'];
		$area2 = $_POST['selectArea2'];
		$area3 = $_POST['selectArea3'];
		$emptyArea1 = empty($area1);

		$otherLanguage = $_POST['inputOtherLanguage'];
		$emptyOtherLanguage = empty($otherLanguage);
		$issetCheckboxSupporter = isset($_POST['checkboxSupporter']);
		$issetCheckboxReader = isset($_POST['checkboxReader']);
		$issetCheckboxAuthor = isset($_POST['checkboxAuthor']);
		$issetCheckboxVolumeEditor = isset($_POST['checkboxVolumeEditor']);
		$issetCheckboxReviewer = isset($_POST['checkboxReviewer']);
		$issetCheckboxProofreader = isset($_POST['checkboxProofreader']);
		$issetCheckboxTypesetter = isset($_POST['checkboxTypesetter']);
		$issetCheckboxNewsletter = isset($_POST['checkboxNewsletter']);
		$issetCheckboxML = isset($_POST['checkboxML']);
		$issetCheckboxEnglish = isset($_POST['checkboxEnglish']);
		$issetCheckboxGerman = isset($_POST['checkboxGerman']);
		$issetCheckboxFrench = isset($_POST['checkboxFrench']);
		$issetCheckboxOther = isset($_POST['checkboxOther']);

		$areaOkay = !$issetCheckboxReviewer || !$emptyArea1;
			
		$languageOkay = false;
		if (!$issetCheckboxReviewer|| 
		($issetCheckboxEnglish||$issetCheckboxGerman||$issetCheckboxFrench||($issetCheckboxOther&&!empty($otherLanguage)))) {
			$languageOkay=true;
		}

?>
