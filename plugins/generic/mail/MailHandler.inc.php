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



class MailHandler extends Handler {	

	function mailtest($args, $request) {
	
		$press = $request->getPress();
	
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('values',$press->getSetting('carolatest'));

		$templateMgr->assign('pageTitle','notification.type.editorAssignmentTask');
		$templateMgr->assign('pageTitle','announcement.announcements');

		$mailPlugin = PluginRegistry::getPlugin('generic', MAIL_PLUGIN_NAME);
		$templateMgr->display($mailPlugin->getTemplatePath().'mail.tpl');


	}

}

?>
