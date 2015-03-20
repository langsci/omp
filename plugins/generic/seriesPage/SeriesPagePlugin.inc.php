<?php

/**
 * @file plugins/generic/seriesPage/SeriesPagePlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesPagePlugin
 *
 */



import('lib.pkp.classes.plugins.GenericPlugin');

class SeriesPagePlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleSeriesPageRequest'));
			}
			return true;
		}
		return false;

	}

	function handleSeriesPageRequest($hookName, $args) {

		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page == 'series') {

			$args[1]='series';
			define('SERIESPAGE_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'SeriesPageHandler');
			$this->import('SeriesPageHandler');

			return true;
		} 
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.seriesPage.displayName');
	}

	function getDescription() {
		return __('plugins.generic.seriesPage.description');
	}

}

?>
