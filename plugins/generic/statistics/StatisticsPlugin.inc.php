<?php

/**
 * @file plugins/generic/statistics/StatisticsPlugin.inc.php
 *
 * Copyright (c) 2014 CeDiS, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class statisticsPlugin
 * @ingroup plugins_generic_langsci
 *
 * @brief langsci plugin to add statistic images at the download tab
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class StatisticsPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.statistics.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.statistics.description');
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
	 * Overwrite the bookSpecs and bookInfo templates
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleReaderTemplateInclude($hookName, $args) {
		$templateMgr =& $args[0];
		$params =& $args[1];
		$templateMgr->assign_by_ref('base_url', Config::getVar('general','base_url'));
		
		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'catalog/book/bookInfo.tpl':
			//	echo $this->getTemplatePath();
				$templateMgr->display($this->getTemplatePath() . 'langsciBookInfo.tpl', 'text/html', 'TemplateManager::include');
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
			case 'catalog/book/book.tpl':
				HookRegistry::register ('TemplateManager::include', array(&$this, 'handleReaderTemplateInclude'));
				break;
		}
		return false;
	}

}

?>
