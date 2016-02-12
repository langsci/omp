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
import('plugins.generic.identificationCodes.IdentificationCodesDAO');

class IdentificationCodesHandler extends Handler {	

	function IdentificationCodesHandler() {
		parent::Handler();
	}

	function identificationCodes($args, $request) {

		$selectedIdentificationCodes = array(15,28,29,30);

		$identificationCodesDAO = new IdentificationCodesDAO();
		$identificationCodes = $identificationCodesDAO->getData();

		$onixCodelistItemDao = DAORegistry::getDAO('ONIXCodelistItemDAO');
		$onixCodes = $onixCodelistItemDao->getCodes('List5');

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.identificationCodes');
		$templateMgr->assign('identificationCodes', $identificationCodes);
		$templateMgr->assign('selectedIdentificationCodes', $selectedIdentificationCodes);
		$templateMgr->assign('onixCodes', $onixCodes);
		$identificationCodesPlugin = PluginRegistry::getPlugin('generic', IDENTIFICATIONCODES_PLUGIN_NAME);
		$templateMgr->display($identificationCodesPlugin->getTemplatePath().'identificationCodes.tpl');
	}
}
?>
