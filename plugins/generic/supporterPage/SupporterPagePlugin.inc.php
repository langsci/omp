<?php

/**
 * @file plugins/generic/supporterPage/SupporterPagePlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SupporterPagePlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class SupporterPagePlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleSupporterPageRequest'));
			}
			return true;
		}
		return false;

	}

	function handleSupporterPageRequest($hookName, $args) {

		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page == 'about' && in_array($op, array('supporters'))) {
			define('SUPPORTERPAGE_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'SupporterPageHandler');
			$this->import('SupporterPageHandler');
			return true;
		} 
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.supporterPage.displayName');
	}

	function getDescription() {
		return __('plugins.generic.supporterPage.description');
	}

}

?>
