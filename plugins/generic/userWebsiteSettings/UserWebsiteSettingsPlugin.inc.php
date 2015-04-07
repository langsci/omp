<?php

/**
 * @file UserWebsiteSettings.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserWebsiteSettingsPlugin
 * User website settings plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class UserWebsiteSettingsPlugin extends GenericPlugin {

	/**
	 * Get the plugin's display (human-readable) name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.userWebsiteSettings.displayName');
	}

	/**
	 * Get the plugin's display (human-readable) description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.userWebsiteSettings.description');
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
				// Register the DAO.
				import('plugins.generic.userWebsiteSettings.UserWebsiteSettingsDAO');
				$userWebsiteSettingsDao = new UserWebsiteSettingsDAO();
				DAORegistry::registerDAO('UserWebsiteSettingsDAO', $userWebsiteSettingsDao);
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
//echo $request->getCompleteUrl();
		$templateMgr = TemplateManager::getManager($request);

		// get url path components
		$pageUrl =& $args[0];
		$opUrl =& $args[1];
		$tailUrl = "/".implode("/",$request->getRequestedArgs());
		$completeUrl = $pageUrl.$opUrl.implode("",$request->getRequestedArgs());

		// get path components specified in the plugin settings
		$settingPathArray = explode("/", $press->getSetting('langsci_userWebsiteSettings_path'));
		$numberOfComponentsSettings = sizeof($settingPathArray);
		$pageSettings = $settingPathArray[0];
		$opSettings = $settingPathArray[1];
		$tailSettings = "";

		if (sizeof($settingPathArray)>2) {
			for ($i=2; $i<sizeof($settingPathArray); $i++) {
				$tailSettings = $tailSettings . "/" . $settingPathArray[$i];	
			}
		}
//echo "<br>url path components : " . $pageUrl . " " . $opUrl . " " .$tailUrl . " ". $completeUrl;
//echo "<br>setting path components : " . $pageSettings . " " . $opSettings . " " .$tailSettings . " ". $numberOfComponentsSettings;

		/* todo: pfade noch checken für view und profile? */
		/* todo: gilt nicht auf unterster Ebene, da dann noch ein "index" an den Pfad angehängt wird! */
		/* Erklärung: muss gemacht werden, da ich den Pfad zur Seite im template brauche und daher nichtausd der db ziehen kann */
		$viewUserWebsiteSettingsRequested = substr($completeUrl,-23)=="viewUserWebsiteSettings";
		$uploadProfileImageRequested = substr($completeUrl,-18)=="uploadProfileImage";

		if ( $viewUserWebsiteSettingsRequested || $uploadProfileImageRequested ||
			  ($numberOfComponentsSettings==1 && $pageUrl==$pageSettings && $opUrl=="index"     && $tailUrl=="/") ||
			  ($numberOfComponentsSettings==2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl=="/") ||
			  ($numberOfComponentsSettings >2 && $pageUrl==$pageSettings && $opUrl==$opSettings && $tailUrl==$tailSettings)
			) {

			$pageUrl = '';
			if ($uploadProfileImageRequested) {
				$opUrl = 'uploadProfileImage';
			}
			else {
				$opUrl = 'viewUserWebsiteSettings';
			}


			define('HANDLER_CLASS', 'UserWebsiteSettingsHandler');
			define('USERWEBSITESETTINGS_PLUGIN_NAME', $this->getName());

			$this->import('UserWebsiteSettingsHandler');

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
			$verbs[] = array('settings', __('plugins.generic.userWebsiteSettings.settings'));
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
					$this->import('UserWebsiteSettingsSettingsForm');
					$form = new UserWebsiteSettingsSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.userWebsiteSettings.settings.saved'));
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

		$langsci_userWebsiteSettings_path = $press->getSetting('langsci_userWebsiteSettings_path'); 
		$langsci_userWebsiteSettings_publicProfile = $press->getSetting('langsci_userWebsiteSettings_publicProfile');  
		$langsci_userWebsiteSettings_email = $press->getSetting('langsci_userWebsiteSettings_email');  
		$langsci_userWebsiteSettings_hallOfFame = $press->getSetting('langsci_userWebsiteSettings_hallOfFame');  
		$langsci_userWebsiteSettings_profileImage = $press->getSetting('langsci_userWebsiteSettings_profileImage');  

		if (isset($langsci_userWebsiteSettings_path) &&
			 isset($langsci_userWebsiteSettings_publicProfile) &&
			 isset($langsci_userWebsiteSettings_email) &&
			 isset($langsci_userWebsiteSettings_hallOfFame) &&
			 isset($langsci_userWebsiteSettings_profileImage)) {
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
