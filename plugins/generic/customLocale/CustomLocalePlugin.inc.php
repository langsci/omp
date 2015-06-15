<?php

/**
 * @file plugins/generic/customLocale/CustomLocalePlugin.inc.php
 *
 * Copyright (c) 2013-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomLocalePlugin
 *
 * @brief This plugin enables customization of locale strings.
 */

define('CUSTOM_LOCALE_DIR', 'customLocale');
import('lib.pkp.classes.plugins.GenericPlugin');

class CustomLocalePlugin extends GenericPlugin {

	function register($category, $path) {

		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {

				// Add custom locale data for already registered locale files.
				$locale = AppLocale::getLocale();
				$localeFiles = AppLocale::getLocaleFiles($locale);

				$press = Request::getPress();
				$pressId = $press->getId();

				$publicFilesDir = Config::getVar('files', 'public_files_dir');
				$customLocalePathBase = $publicFilesDir . DIRECTORY_SEPARATOR . 'presses' . DIRECTORY_SEPARATOR . $pressId . DIRECTORY_SEPARATOR . CUSTOM_LOCALE_DIR . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR;

				import('lib.pkp.classes.file.FileManager');
				$fileManager = new FileManager();
				foreach ($localeFiles as $localeFile) {
					$customLocalePath = $customLocalePathBase . $localeFile->getFilename();
					if ($fileManager->fileExists($customLocalePath)) {
						AppLocale::registerLocaleFile($locale, $customLocalePath, false);
					}
				}

				// Add custom locale data for all locale files registered after this plugin
				HookRegistry::register('PKPLocale::registerLocaleFile', array(&$this, 'addCustomLocale'));
				HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
				HookRegistry::register('Templates::Management::Settings::website', array($this, 'callbackShowWebsiteSettingsTabs'));
				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));
			}

			return true;
		}
		return false;
	}

	/**
	 * Permit requests to the static pages grid handler
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $args) {

		$component =& $args[0];

		if ($component == 'plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler') {
			// Allow the custom locale grid handler to get the plugin object
			import($component);
			CustomLocaleGridHandler::setPlugin($this);
			return true;
		} 
		return false;
	}

	function callbackHandleContent($hookName, $args) {

		$request = $this -> getRequest();
		$press   = $request -> getPress();		

		$templateMgr = TemplateManager::getManager($request);

		// get url path components
		$page =& $args[0];
		$op =& $args[1];
		$tail = "/".implode("/",$request->getRequestedArgs());

		if ($page=="manager" && $op=="plugin" && $tail=="/generic/customlocaleplugin/printChanges") {

			$op = 'printCustomLocaleChanges';
			define('HANDLER_CLASS', 'CustomLocaleHandler');
			define('CUSTOMLOCALE_PLUGIN_NAME', $this->getName());
			$this->import('CustomLocaleHandler');

		}
		return false;	
	}

	/**
	 * @copydoc Plugin::getManagementVerbLinkAction()
	 */
	function getManagementVerbLinkAction($request, $verb) {
		list($verbName, $verbLocalized) = $verb;

		switch ($verbName) {
			case 'index':
				// Generate a link action for the "settings" action
				$dispatcher = $request->getDispatcher();
				import('lib.pkp.classes.linkAction.request.RedirectAction');
				return new LinkAction(
					$verbName,
					new RedirectAction($dispatcher->url(
						$request, ROUTE_PAGE,
						null, 'management', 'settings', 'website',
						array('uid' => uniqid()), // Force reload
						'customLocale' // Anchor for tab
					)),
					$verbLocalized,
					null
				);		
			default:
				return parent::getManagementVerbLinkAction($request, $verb);
		}
	}

	function addCustomLocale($hookName, $args) {
		$locale =& $args[0];
		$localeFilename =& $args[1];

		$press = Request::getPress();
		$pressId = $press->getId();

		$publicFilesDir = Config::getVar('files', 'public_files_dir');
		$customLocalePath = $publicFilesDir . DIRECTORY_SEPARATOR . 'presses' . DIRECTORY_SEPARATOR . $pressId . DIRECTORY_SEPARATOR . CUSTOM_LOCALE_DIR . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $localeFilename;

		import('lib.pkp.classes.file.FileManager');
		$fileManager = new FileManager();
		if ($fileManager->fileExists($customLocalePath)) {
			AppLocale::registerLocaleFile($locale, $customLocalePath, false);
		}

		return true;
	}

	function getDisplayName() {
		return __('plugins.generic.customLocale.name');
	}

	function getDescription() {
		return __('plugins.generic.customLocale.description');
	}


	/**
	 * Extend the website settings tabs to include static pages
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackShowWebsiteSettingsTabs($hookName, $args) {
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();

		// Add a new tab for static pages
		$output .= '<li><a name="customLocale" href="' . $dispatcher->url($request, ROUTE_COMPONENT, null, 'plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler', 'index') . '">' . __('plugins.generic.customLocale.customLocale') . '</a></li>';

		// Permit other plugins to continue interacting with this hook
		return false;
	}

	function getManagementVerbs() {

		$verbs = parent::getManagementVerbs();

		if ($this->getEnabled()) {
			$verbs[] = array('index', __('plugins.generic.customLocale.customize'));
			$verbs[] = array('printChanges', __('plugins.generic.customLocale.printChanges'));
		}

		return $verbs;
	}


	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

}

?>
