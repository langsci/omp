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

class CatalogEntryTabPlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {

				import('plugins.generic.catalogEntryTab.CatalogEntryTabDAO');
				$catalogEntryTabDao = new CatalogEntryTabDAO();
				DAORegistry::registerDAO('CatalogEntryTabDAO', $catalogEntryTabDao);

				HookRegistry::register('LoadComponentHandler', array($this, 'setupTabHandler'));
				HookRegistry::register('Templates::Controllers::Modals::SubmissionMetadata::CatalogEntryTabs::Tabs', array($this, 'addTab'));
			}
			return true;
		}
		return false;
	}



	function addTab($hookName, $args) {

		$output =& $args[2];
		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);
		$output .= $templateMgr->fetch($this->getTemplatePath() . 'additionalTab.tpl');
		   
		return true;
	}

	/**
	 * Permit requests to the plugin tab handler
	 * @param $hookName string The name of the hook being invoked &quot;
	 * @param $args array The parameters to the invoked hook
	 */
	function setupTabHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.catalogEntryTab.controllers.AdditionalTabHandler') {
			// Allow the grid handler to get the plugin object
			import($component);
			AdditionalTabHandler::setPlugin($this);
			return true;
		}
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.catalogEntryTab.displayName');
	}

	function getDescription() {
		return __('plugins.generic.catalogEntryTab.description');
	}

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
