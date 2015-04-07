<?php

/**
 * @file PublicProfilesPlugin.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PublicProfilesPlugin
 * Public Profiles plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class PublicProfilesPlugin extends GenericPlugin {

	/**
	 * Get the plugin's display (human-readable) name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.publicProfiles.displayName');
	}

	/**
	 * Get the plugin's display (human-readable) description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.publicProfiles.description');
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

		// check user id in url
		$url = array();
		$url[] = $args[0];
		$url[] = $args[1];
		$url = array_merge($url,$request->getRequestedArgs());
		$userId = $url[sizeof($url)-1];
		if (!ctype_digit ($userId)) {
			return false;
		} 

		// get url path components
		$pageUrl =& $args[0];
		$opUrl =& $args[1];
		$tailUrl = "/".implode("/",$request->getRequestedArgs());
		if (sizeof($url)>2) {
			$tailUrl = substr($tailUrl,0,-strlen($userId));
		}
		if (substr($tailUrl,-1)=="/" && strlen($tailUrl)>1) {
			$tailUrl = substr($tailUrl,0,-1);
		}

		// get path components specified in the plugin settings
		$settingPathArray = explode("/", $press->getSetting('langsci_publicProfiles_path'));
		$numberOfComponentsSettings = sizeof($settingPathArray);
		$pageSettings = $settingPathArray[0];
		$opSettings = $settingPathArray[1];
		$tailSettings = "";

		if (sizeof($settingPathArray)>2) {
			for ($i=2; $i<sizeof($settingPathArray); $i++) {
				$tailSettings = $tailSettings . "/" . $settingPathArray[$i];	
			}
		}

		if ( (substr($completeUrl,-17)=="viewPublicProfile") ||
			  ($numberOfComponentsSettings==1 && $pageUrl==$pageSettings && $opUrl==$userId    && $tailUrl=="/") ||
			  ($numberOfComponentsSettings==2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl=="/") ||
			  ($numberOfComponentsSettings >2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl==$tailSettings)
			) {

			$pageUrl = '';
			$opUrl = 'viewPublicProfile';

			define('HANDLER_CLASS', 'PublicProfilesHandler');
			define('PUBLICPROFILES_PLUGIN_NAME', $this->getName());

			$this->import('PublicProfilesHandler');

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
			$verbs[] = array('settings', __('plugins.generic.publicProfiles.settings'));
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
					$this->import('PublicProfilesSettingsForm');
					$form = new PublicProfilesSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.publicProfiles.settings.saved'));
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

		$langsci_publicProfiles_path = $press->getSetting('langsci_publicProfiles_path');
		$langsci_publicProfiles_userGroups = $press->getSetting('langsci_publicProfiles_userGroups');
		$langsci_publicProfiles_onlyPublishedMonographs = $press->getSetting('langsci_publicProfiles_onlyPublishedMonographs');
		$langsci_publicProfiles_unifiedStyleSheetForLinguistics = $press->getSetting('langsci_publicProfiles_unifiedStyleSheetForLinguistics');


		if (isset($langsci_publicProfiles_path) &&
			isset($langsci_publicProfiles_userGroups) && 
			isset($langsci_publicProfiles_onlyPublishedMonographs) &&
			isset($langsci_publicProfiles_unifiedStyleSheetForLinguistics)) {
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
