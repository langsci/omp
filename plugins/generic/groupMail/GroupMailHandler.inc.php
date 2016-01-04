<?php 

/**
 * @file plugins/generic/groupMail/GroupMailHandler.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING. 
 *
 * @class GroupMailHandler
 *
 *
 */

import('classes.handler.Handler');
import('plugins.generic.groupMail.GroupMailDAO');
import('plugins.generic.groupMail.GroupMailSettingsForm');

class GroupMailHandler extends Handler {	

	function GroupMailHandler() {
		parent::Handler();
	}

	function viewGroupMail($args, $request) {

		// redirect to index page if user is not 1,4,86,172,176,193
		$user = $request->getUser();
		if (!$user || ($user && !in_array($user->getId(), array(1,4,86,172,176,193)))) {
			$request->redirect(null, 'index');
		}

		$press = $request->getPress();
		$templateMgr = TemplateManager::getManager($request);

		$groupMailDAO = new GroupMailDAO;
		$userGroups = $groupMailDAO->getUserGroups();

		$emptyCheckboxes = array();
		for ($i=0; $i<sizeof($userGroups);$i++) {
			$emptyCheckboxes[] = false;
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.groupMail');
		$templateMgr->assign('userGroups', $userGroups);
		$templateMgr->assign('baseUrl', $request->getBaseUrl());
		$templateMgr->assign('postOr', $emptyCheckboxes);
		$templateMgr->assign('postAnd', $emptyCheckboxes);
		$templateMgr->assign('postNot', $emptyCheckboxes);
		$templateMgr->assign('getUsernames',true);
		$templateMgr->assign('getEmails',true);
		$templateMgr->assign('getEmails',true);
		$templateMgr->assign('results',null);

		$groupMailPlugin = PluginRegistry::getPlugin('generic', GROUPMAIL_PLUGIN_NAME);
		$templateMgr->display($groupMailPlugin->getTemplatePath().'groupMail.tpl');
	}

	function getGroupMailResults($args, $request) {

		// redirect to index page if user is not 1,4,86,172,176,193
		$user = $request->getUser();
		if (!$user || ($user && !in_array($user->getId(), array(1,4,86,172,176,193)))) {
			$request->redirect(null, 'index');
		}

		$groupMailDAO = new GroupMailDAO;
		$userGroups = $groupMailDAO->getUserGroups();
		$keysUserGroups = array_keys($userGroups);		

		$saveToFile = isset($_REQUEST['buttonSaveToFile']);
		$showResults = isset($_REQUEST['buttonShowResults']);
		$getUsernames = $_POST['getUsernames'];
		$getEmails = $_POST['getEmails'];

		$or = array();
		$and = array();
		$not = array();
		$postOr = array();
		$postAnd = array();
		$postNot = array();	
		for ($i=0; $i<sizeof($userGroups);$i++) {
			$postOr[$i] = $_POST['OR'.$keysUserGroups[$i]];
			$postAnd[$i] = $_POST['AND'.$keysUserGroups[$i]];
			$postNot[$i] = $_POST['NOT'.$keysUserGroups[$i]];

			if ($_POST['OR'.$keysUserGroups[$i]]) {
				$or[] = $keysUserGroups[$i];
			}
			if ($_POST['AND'.$keysUserGroups[$i]]) {
				$and[] = $keysUserGroups[$i];
			}
			if ($_POST['NOT'.$keysUserGroups[$i]]) {
				$not[] = $keysUserGroups[$i];
			}
		}

		$emailsAnd = array();
		if (sizeof($and)>0) {		

			$query = "";
			$pos0 = true;
			for ($i=0; $i<sizeof($and);$i++) {
				if ($pos0) {
					$query = $query . "(select user_id from user_user_groups where user_group_id=".$and[$i].") ";
					$pos0=false;
				} else {
					$query =  " (select user_id from user_user_groups where user_group_id=".$and[$i]." and user_id in " . $query . ")";
				}
			}
			$query = "SELECT first_name, last_name, email from users where user_id IN " . $query . ";";

			$res = $groupMailDAO->getEmailsByGroup($query);	
			if ($res) {
				$emailsAnd = $res;
			}
		}

		$emailsOr = array();
		if (sizeof($or)>0) {
			$emailsOr  = $groupMailDAO->getEmailsByGroup('SELECT first_name, last_name, email FROM users WHERE user_id IN (SELECT user_id FROM user_user_groups WHERE user_group_id IN ('.implode(",",$or).'));');
		}
		$emailsNot = array();
		if (sizeof($not)>0) {
			$emailsNot  = $groupMailDAO->getEmailsByGroup('SELECT first_name, last_name, email FROM users WHERE user_id IN (SELECT user_id FROM user_user_groups WHERE user_group_id IN ('.implode(",",$not).'));');
		}

		$results = array_unique(array_diff(array_merge($emailsOr,$emailsAnd),$emailsNot));

		if ($showResults) {
			$userGroups = $groupMailDAO->getUserGroups();
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->assign('pageTitle', 'plugins.generic.title.groupMail');
			$templateMgr->assign('userGroups', $userGroups);
			$templateMgr->assign('results', $results);
			$templateMgr->assign('postOr', $postOr);
			$templateMgr->assign('postAnd', $postAnd);
			$templateMgr->assign('postNot', $postNot);
			$templateMgr->assign('getUsernames', $getUsernames);
			$templateMgr->assign('getEmails', $getEmails);	
			$templateMgr->assign('baseUrl', $request->getBaseUrl());

			$groupMailPlugin = PluginRegistry::getPlugin('generic', GROUPMAIL_PLUGIN_NAME);
			$templateMgr->display($groupMailPlugin->getTemplatePath().'groupMail.tpl');

		} elseif ($saveToFile) {

			$output = "Results: \n";
			if ($results && ($getUsernames||$getEmails)) {

			 	while ($username = current($results)) {
					if ($getUsernames) {
						$output = $output . $username . " ";		
					}
					if ($getEmails) {
						$output = $output . key($results);
					}
					next($results);
					$output = $output . "\n";
				}
			}
			
			$filename = 'groupMailResult.txt';
			ob_end_clean();
			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Length: " . strlen($output));
			echo $output;
		}
	}

}
?>
