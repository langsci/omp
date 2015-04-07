<?php

/**
 * @file plugins/generic/registerPage/includes/assignSubscriptionData.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * assigns variables to template manager
 */

	$templateMgr->assign('areas',$areas);
	$templateMgr->assign('area1',$area1);
	$templateMgr->assign('area2',$area2);
	$templateMgr->assign('area3',$area3);
	$templateMgr->assign('issetCheckboxEnglish',$issetCheckboxEnglish);
	$templateMgr->assign('issetCheckboxGerman',$issetCheckboxGerman);
	$templateMgr->assign('issetCheckboxFrench',$issetCheckboxFrench);
	$templateMgr->assign('issetCheckboxOther',$issetCheckboxOther);
	$templateMgr->assign('otherLanguage',$otherLanguage);
	$templateMgr->assign('languageOkay',$languageOkay);
	$templateMgr->assign('emptyArea1',$emptyArea1);
?>
