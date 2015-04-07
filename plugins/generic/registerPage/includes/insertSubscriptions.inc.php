<?php

/**
 * @file plugins/generic/registerPage/includes/insertSubscriptions.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * short code to insert subscription data to database
 */

		if ($issetCheckboxNewsletter) {$registerPageDAO->insertSubscription($userId,'Newsletter');}
		if ($issetCheckboxML) {$registerPageDAO->insertSubscription($userId,'General Mailing List');}

		if ($issetCheckboxSupporter) {$registerPageDAO->insertUserGroup($userId,'Supporter');}
		if ($issetCheckboxReader)  {$registerPageDAO->insertUserGroup($userId,'Reader');}
		if ($issetCheckboxProofreader)  {$registerPageDAO->insertUserGroup($userId,'Proofreader');}
		if ($issetCheckboxTypesetter)  {$registerPageDAO->insertUserGroup($userId,'Typesetter');}
		if ($issetCheckboxAuthor)  {$registerPageDAO->insertUserGroup($userId,'Author');}
		if ($issetCheckboxVolumeEditor)  {$registerPageDAO->insertUserGroup($userId,'Volume Editor');}

?>
