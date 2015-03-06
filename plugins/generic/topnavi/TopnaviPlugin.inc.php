<?php

/**
 * @file plugins/generic/topnavi/TopnaviPlugin.inc.php
 *
 * Copyright (c) 2014 Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class TopnaviPlugin
 *
 * @brief topnavi plugin for all topnavi changes
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class TopnaviPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.topnavi.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.topnavi.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {

		if (parent::register($category, $path)) {

			if ($this->getEnabled()) {
				HookRegistry::register ('TemplateManager::include', array(&$this, 'handleTopnaviTemplateInclude'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Overwrite the bookSpecs and bookInfo templates
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleTopnaviTemplateInclude($hookName, $args) {

		$templateMgr =& $args[0];
		$params =& $args[1];

		if (!isset($params['smarty_include_tpl_file'])) return false;

		switch ($params['smarty_include_tpl_file']) {
			case 'header/localnav.tpl':
				$templateMgr->display($this->getTemplatePath() . 'localnavModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
		}
		return false;
	}



}

?>
