<?php

/**
 * @file StaticPagesPlugin.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.staticPages
 * @class StaticPagesPlugin
 * Static pages plugin main class
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
				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Declare the handler function to process the actual page PATH
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackHandleContent($hookName, $args) {

		$request = $this -> getRequest();
		$press   = $request -> getPress();		

		$templateMgr = TemplateManager::getManager($request);

		// get url path components
		$pageUrl =& $args[0];
		$opUrl =& $args[1];
		$tailUrl = "/".implode("/",$request->getRequestedArgs());

		// get path components specified in the plugin settings
		$settingPathArray = explode("/", $press->getSetting('pathHallOfFame'));
		$numberOfComponentsSettings = sizeof($settingPathArray);
		$pageSettings = $settingPathArray[0];
		$opSettings = $settingPathArray[1];
		$tailSettings = "";
		if (sizeof($settingPathArray) >2) {
			for ($i=2; $i<sizeof($settingPathArray); $i++) {
				$tailSettings = $tailSettings . "/" . $settingPathArray[$i];	
			}
		}
//echo "<br>vor if: " . $pageSettings . " " . $opSettings . " " .$tailSettings . " ". $numberOfComponentsSettings;
		if ( ($numberOfComponentsSettings==1 && $pageUrl==$pageSettings && $opUrl=="index"     && $tailUrl=="/") ||
			 ($numberOfComponentsSettings==2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl=="/") ||
			 ($numberOfComponentsSettings >2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl==$tailSettings)
			) {

			$pageUrl = '';
			$opUrl = 'viewHallOfFame';

			define('HANDLER_CLASS', 'HallOfFameHandler');
			define('HALLOFFAME_PLUGIN_NAME', $this->getName());

			$this->import('HallOfFameHandler');

		return true;

		}

	}

	
	/**
	 * @copydoc PKPPlugin::getManagementVerbs()
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.hallOfFame.settings'));
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

	/**
	 * Determines if statistics settings have been enabled for this plugin.
	 * @param $press Press
	 * @return boolean
	 */ 
	function statsConfigured($press) {

		$userGroupsHallOfFame = $press->getSetting('userGroupsHallOfFame');
		$pathHallOfFame = $press->getSetting('pathHallOfFame');

		if (isset($pathHallOfFame) && isset($userGroupsHallOfFame)) {
			return true;
		}

		return false;
	}


	/**
	 * Get the name of the settings file to be installed on new press
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the filename of the ADODB schema for this plugin.
	 * @return string Full path and filename to schema descriptor.
	 *//*
	function getInstallSchemaFile() {
		return $this->getPluginPath() . '/schema.xml';
	}*/

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

	/**
	 * Get the JavaScript URL for this plugin.
	 *//*
	function getJavaScriptURL($request) {
		return $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js';
	}*/
}

?>
