<?php

/**
 * @file plugins/generic/seriesOverview/SeriesOverviewPlugin.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesOverviewPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class SeriesOverviewPlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleLoadRequest'));
				HookRegistry::register ('TemplateManager::display',
						array(&$this, 'handleDisplayTemplate'));
			}
			return true;
		}
		return false;

	}

	function handleDisplayTemplate($hookName, $args) {

		$request = $this->getRequest();
		$press = $request->getPress();
		$imageOnSeriesPages = $press->getSetting('langsci_seriesOverview_imageOnSeriesPages');
		$pathSettings = $press->getSetting('langsci_seriesOverview_path');
		$setTabTitle = $press->getSetting('langsci_seriesOverview_setTabTitle');

		$templateMgr =& $args[0];
		$template =& $args[1];

		switch ($template) {

			case 'catalog/series.tpl':
				$templateMgr->assign('imageOnSeriesPages',$imageOnSeriesPages);	
				$templateMgr->assign('setTabTitle',$setTabTitle);	
				$templateMgr->display($this->getTemplatePath() . 
				'seriesModified2.tpl', 'text/html', 'TemplateManager::display');
				return true;
		}
		return false;
	}

	function handleLoadRequest($hookName, $args) {

		$request = $this->getRequest();
		$press   = $request->getPress();		

		// get url path components to overwrite them 
		$pageUrl =& $args[0];
		$opUrl =& $args[1];

		// get path components
		$urlArray = array();
		$urlArray[] = $args[0];
		$urlArray[] = $args[1];
		$urlArray = array_merge($urlArray,$request->getRequestedArgs());
		$urlArrayLength = sizeof($urlArray);

		// get path components specified in the plugin settings
		$settingPath = $press->getSetting('langsci_seriesOverview_path');
		if (!ctype_alpha(substr($settingPath,0,1))&&!ctype_digit(substr($settingPath,0,1))) {
			return false;
		}
		$settingPathArray = explode("/",$settingPath);
		$settingPathArrayLength = sizeof($settingPathArray);
		if ($settingPathArrayLength==1) {
			$settingPathArray[] = 'index';
		}

		// compare path and path settings
		$goToSeriesOverview = false;
		if ($settingPathArray==$urlArray){
			$goToSeriesOverview = true;
		}

		if ($goToSeriesOverview) {

			$pageUrl = '';
			$opUrl = 'viewSeriesOverview';

			define('HANDLER_CLASS', 'SeriesOverviewHandler');
			define('SERIESOVERVIEW_PLUGIN_NAME', $this->getName());

			$this->import('SeriesOverviewHandler');

			return true;
		}
		return false;
	}

	/**
	 * @see Plugin::getActions()
	 */
/*	function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $verb)
		);
	}*/

 	/**
	 * @see Plugin::manage()
	 */
/*	function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$context = $request->getContext();
				$this->import('SettingsForm');
				$form = new SettingsForm($this, $context->getId());

				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						return new JSONMessage(true);
					}
				} else {
					$form->initData();
				}
				return new JSONMessage(true, $form->fetch($request));
		}
		return parent::manage($args, $request);
	}*/


	/**
	 * @copydoc PKPPlugin::getManagementVerbs()
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.seriesOverview.settings'));
		}
		return $verbs;
	}

	/**
	 * Define management link actions for the settings verb.
	 * @param $request PKPRequest
	 * @param $verb string
	 * @return LinkAction
	 */ 
	function getManagementVerbLinkAction($request, $verb) {
		$router = $request->getRouter();

		list($verbName, $verbLocalized) = $verb;

		if ($verbName === 'settings') {
			import('lib.pkp.classes.linkAction.request.AjaxLegacyPluginModal');
			$actionRequest = new AjaxLegacyPluginModal(
				$router->url($request, null, null, 'plugin', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
				$this->getDisplayName()
			);
			return new LinkAction($verbName, $actionRequest, $verbLocalized, null);
		}
		return null;
	}

	/**
	 * @copydoc PKPPlugin::manage()
	 */
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		$request = $this->getRequest();
		$press = $request->getPress();
		$templateMgr = TemplateManager::getManager($request);

		switch ($verb) {

			case 'settings':
					$this->import('SeriesOverviewSettingsForm');
					$form = new SeriesOverviewSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.seriesOverview.form.saved'));
							return false;
						} else {
							$pluginModalContent = $form->fetch($request);
						}
					} else {
						$form->initData();
						$pluginModalContent = $form->fetch($request);
					}

				return true;


			default:
				// let the parent handle it.
				return parent::manage($verb, $args, $message, $messageParams);
		}
	}

	function getDisplayName() {
		return __('plugins.generic.seriesOverview.displayName');
	}

	function getDescription() {
		return __('plugins.generic.seriesOverview.description');
	}

	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
