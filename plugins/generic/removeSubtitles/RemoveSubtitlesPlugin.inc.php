<?php

/**
 * @file plugins/generic/RemoveSubtitles/RemoveSubtitlesPlugin.inc.php
 *
 * Copyright (c) 2014 CeDiS, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CiteAsPlugin
 * @ingroup plugins_generic_langsci
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class RemoveSubtitlesPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.removeSubtitles.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.removeSubtitles.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				HookRegistry::register ('TemplateManager::display', array(&$this, 'handleTemplateDisplay'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Overwrite the monograph templates
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleReaderTemplateInclude($hookName, $args) {
		$templateMgr =& $args[0];
		$params =& $args[1];
		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'catalog/monographs.tpl':
				HookRegistry::register ('TemplateManager::include', array(&$this, 'handleReaderTemplateInclude'));
				break;
			case 'catalog/monograph.tpl':
				$templateMgr->display($this->getTemplatePath() . 'langsciMonograph.tpl', 'text/html', 'TemplateManager::include');
				return true;
		}
		return false;
	}

	/**
	 * Hook callback: Handle requests.
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleTemplateDisplay($hookName, $args) {
		$templateMgr =& $args[0];
		$template =& $args[1];

		switch ($template) {
			case 'catalog/index.tpl':
				HookRegistry::register ('TemplateManager::include', array(&$this, 'handleReaderTemplateInclude'));
				break;
		}
		return false;
	}

}

?>
