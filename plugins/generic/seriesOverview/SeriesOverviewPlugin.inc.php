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
		$press = $request->getPress();

		// get path components from the url in the browser
		$pageUrl =& $args[0];
		$opUrl =& $args[1];
		$tailUrl = "/".implode("/",$request->getRequestedArgs());

		// get path components specified in the plugin settings
		$pathArraySettings = array();
		$pathSettings = $press->getSetting('langsci_seriesOverview_path');

		if (strlen($pathSettings)>0) {
			$pathArraySettings = explode("/", $pathSettings);
		}
		$numberOfComponentsSettings = sizeof($pathArraySettings);

		if ($numberOfComponentsSettings>0) {
			$pageSettings = $pathArraySettings[0];
		} 

		if ($numberOfComponentsSettings>1) {
			$opSettings = $pathArraySettings[1];
		} 

		$tailSettings = "";
		if ($numberOfComponentsSettings>2) {
			for ($i=2; $i<$numberOfComponentsSettings; $i++) {
				$tailSettings = $tailSettings . "/" . $pathArraySettings[$i];	
			}
		}

		if (($numberOfComponentsSettings==1 && $pageUrl==$pageSettings && $opUrl=="index"     && $tailUrl=="/") ||
			 ($numberOfComponentsSettings==2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl=="/") ||
			 ($numberOfComponentsSettings >2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl==$tailSettings)
			) {

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
