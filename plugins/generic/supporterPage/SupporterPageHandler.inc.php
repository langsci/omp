<?php

/**
 * @file plugins/generic/supporterPage/SupporterPageHandler.inc.php
 *
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SupporterPageHandler
 *
 * Find the content and display the appropriate page
 *
 */

import('classes.handler.Handler');
import('plugins.generic.supporterPage.SupporterPageDAO');


class SupporterPageHandler extends Handler {	

	function supporters($args, $request) {

		// prepare template
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.title.supporterPage');

		// get data from database
		$supporterPageDAO = new SupporterPageDAO;
		$supporterNames = $supporterPageDAO->getSupporterNames();
		$supporterAcedemics = $supporterPageDAO->getSupporterAcademics();
		$supporterAffiliation = $supporterPageDAO->getSupporterAffiliation();
		$supporterProminent = $supporterPageDAO->getSupporterProminent();
		$supporterUrl = $supporterPageDAO->getSupporterUrl();

		$supporterStringProm = "";
		$supporterStringNotp = "";
		
		$supporterUserIds =  array_keys($supporterNames);
		for ($i=0; $i<=sizeof($supporterUserIds)-1; $i++) {

			$insertAffiliation = "";
			if (!$supporterAffiliation[$supporterUserIds[$i]]=="") {
				$insertAffiliation = " (".$supporterAffiliation[$supporterUserIds[$i]].")";
			}
			$insertLink1 = "";
			$insertLInk2 = "";
			if (!$supporterUrl[$supporterUserIds[$i]]=="") {
				$insertLink1 = "<a href=".$supporterUrl[$supporterUserIds[$i]].">";
				$insertLInk2 = "</a>";
			}


			if ($supporterProminent[$supporterUserIds[$i]]) {
				$supporterStringProm .= "<li>".$insertLink1.$supporterNames[$supporterUserIds[$i]].$insertLInk2.", ".$supporterAcedemics[$supporterUserIds[$i]].$insertAffiliation."</li>";
			} else {
				$supporterStringNotp .= "<li>".$insertLink1.$supporterNames[$supporterUserIds[$i]].$insertLInk2.", ".$supporterAcedemics[$supporterUserIds[$i]].$insertAffiliation."</li>";
			}
		}
		$supporterString .= "<ol>" . $supporterStringProm . $supporterStringNotp . "</ol>";

		// assign variables and display template
		$templateMgr->assign('intro', __('plugins.generic.supporterPage.intro'));
		$templateMgr->assign('supporter',$supporterString);
 		$this->assignPressUrl($templateMgr,'about');
		$this->displayTemplate($templateMgr,'supporters.tpl');
	}

	function assignPressUrl($templateMgr,$cut) {
 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI],$cut)-1);
		$templateMgr->assign('pressUrl',$host ."/".$shortUrl);	
	}

	function getTemplateManager($request)	{
		$this->validate();
		$press = $request->getPress();
		$this->setupTemplate($request, $press);
		$contentOnly = $request->getUserVar('contentOnly');
		$templateMgr = TemplateManager::getManager($request);
		$server_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 	
		$templateMgr->assign('langsciUrl',substr($server_path,0,strpos($server_path, 'general')));	
		return $templateMgr;
	}

	function displayTemplate($templateMgr,$filename) {
		$supporterPagePlugin = PluginRegistry::getPlugin('generic', SUPPORTERPAGE_PLUGIN_NAME);
		$templateMgr->display($supporterPagePlugin->getTemplatePath().$filename);
	}

}

?>
