<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFamePlugin.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFamePlugin
 * Hall of fame plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class HallOfFamePlugin extends GenericPlugin {

	/**
	 * Get the plugin's display (human-readable) name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.hallOfFame.displayName');
	}

	/**
	 * Get the plugin's display (human-readable) description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.hallOfFame.description');
	}

	/**
	 * Register the plugin, attaching to hooks as necessary.
	 * @param $category string
	 * @param $path string
	 * @return boolean
	 */
	function register($category, $path) {

		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				// Register the hall of fame DAO.
				import('plugins.generic.hallOfFame.HallOfFameDAO');
				$hallOfFameDao = new HallOfFameDAO();
				DAORegistry::registerDAO('HallOfFameDAO', $hallOfFameDao);
				HookRegistry::register('LoadHandler', array($this, 'handleLoadRequest'));
			}
			return true;
		}
		return false;
	}

	// handle load request
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
		$settingPath = $press->getSetting('langsci_hallOfFame_path');
		if (!ctype_alpha(substr($settingPath,0,1))&&!ctype_digit(substr($settingPath,0,1))) {
			return false;
		}
		$settingPathArray = explode("/",$settingPath);
		$settingPathArrayLength = sizeof($settingPathArray);
		if ($settingPathArrayLength==1) {
			$settingPathArray[] = 'index';
		}

		// compare path and path settings
		$goToHallOfFame = false;
		if ($settingPathArray==$urlArray){
			$goToHallOfFame = true;
		}

		if ($goToHallOfFame) {

			$pageUrl = '';
			$opUrl = 'viewHallOfFame';

			define('HANDLER_CLASS', 'HallOfFameHandler');
			define('HALLOFFAME_PLUGIN_NAME', $this->getName());

			$this->import('HallOfFameHandler');

			return true;
		}
		return false;
	}

	
	// PKPPlugin::getManagementVerbs()
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.hallOfFame.settings'));
		}
		return $verbs;
	}

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

	// PKPPlugin::manage()
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		$request = $this->getRequest();
		$press = $request->getPress();
		$templateMgr = TemplateManager::getManager($request);

		switch ($verb) {

			case 'settings':
					$this->import('HallOfFameSettingsForm');
					$form = new HallOfFameSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.hallOfFame.form.saved'));
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

	// Get the name of the settings file to be installed on new press
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	// PKPPlugin::getTemplatePath
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
