<?php

/**
 * @file plugins/generic/groupMail/GroupMailPlugin.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING. 
 *
 * @class GroupMailPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class GroupMailPlugin extends GenericPlugin {


	function register($category, $path) {

		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleLoadRequest'));
			}
			return true;
		}
		return false;

	}

	function handleLoadRequest($hookName, $args) {

		$request = $this->getRequest();
		$press = $request -> getPress();

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
		$settingPath = $press->getSetting('langsci_groupMail_path');
		if (!ctype_alpha(substr($settingPath,0,1))&&!ctype_digit(substr($settingPath,0,1))) {
			return false;
		}
		$settingPathArray = explode("/",$settingPath);
		$settingPathArrayLength = sizeof($settingPathArray);
		if ($settingPathArrayLength==1) {
			$settingPathArray[] = 'index';
		}

		// compare path and path settings
		$goToGroupMail = false;
		if ($settingPathArray==$urlArray){
			$goToGroupMail = true;
		}

		$getResults = in_array('getGroupMailResults',$urlArray);
		if ($goToGroupMail || $getResults) {

			$pageUrl = '';
			if ($getResults) {
				$opUrl = 'getGroupMailResults';
			} else {
				$opUrl = 'viewGroupMail';
			}

			define('HANDLER_CLASS', 'GroupMailHandler');
			define('GROUPMAIL_PLUGIN_NAME', $this->getName());

			$this->import('GroupMailHandler');

			return true;
		}
		return false;
	}

	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.groupMail.settings'));
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
					$this->import('GroupMailSettingsForm');
					$form = new GroupMailSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.groupMail.form.saved'));
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
		return __('plugins.generic.groupMail.displayName');
	}

	function getDescription() {
		return __('plugins.generic.groupMail.description');
	}

	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
