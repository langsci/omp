<?php

/**
 * @file plugins/generic/userWebsiteSettings/UserWebsiteSettingsSettingsForm.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserWebsiteSettingsSettingsForm
 *
 * @brief Form for adding/editing the settings for the user website settings plugin
 */

import('lib.pkp.classes.form.Form');

class UserWebsiteSettingsSettingsForm extends Form {
	/** @var Press The press associated with the plugin being edited */
	var $_press;

	/** @var Plugin The plugin being edited */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin Plugin
	 * @param $press Press
	 */
	function UserWebsiteSettingsSettingsForm($plugin, $press) {
		parent::Form($plugin->getTemplatePath() . 'settings.tpl');
		$this->setPress($press);
		$this->setPlugin($plugin);

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
	}

	//
	// Getters and Setters
	//
	/**
	 * Get the Press.
	 * @return Press
	 */
	function getPress() {
		return $this->_press;
	}

	/**
	 * Set the Press.
	 * @param Press
	 */
	function setPress($press) {
		$this->_press = $press;
	}

	/**
	 * Get the plugin.
	 * @return AddThisBlockPlugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}

	/**
	 * Set the plugin.
	 * @param AddThisBlockPlugin $plugin
	 */
	function setPlugin($plugin) {
		$this->_plugin = $plugin;
	}

	//
	// Overridden template methods
	//
	/**
	 * Initialize form data from the plugin.
	 */
	function initData() {
		$plugin = $this->getPlugin();
		$press = $this->getPress();

		if (isset($plugin)) {
			$this->_data = array(
				'langsci_userWebsiteSettings_path' => $press->getSetting('langsci_userWebsiteSettings_path'),
				'langsci_userWebsiteSettings_publicProfile' => $press->getSetting('langsci_userWebsiteSettings_publicProfile'),
				'langsci_userWebsiteSettings_email' => $press->getSetting('langsci_userWebsiteSettings_email'),
				'langsci_userWebsiteSettings_hallOfFame' => $press->getSetting('langsci_userWebsiteSettings_hallOfFame'),
				'langsci_userWebsiteSettings_profileImage' => $press->getSetting('langsci_userWebsiteSettings_profileImage'),
			);
		}
	}

	/**
	 * Fetch the form.
	 * @see Form::fetch()
	 * @param $request PKPRequest
	 */
	function fetch($request) {
		$plugin = $this->getPlugin();
		$press = $this->getPress();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $plugin->getName());
		$templateMgr->assign('pluginBaseUrl', $request->getBaseUrl() . '/' . $plugin->getPluginPath());

		foreach ($this->_data as $key => $value) {
			$templateMgr->assign($key, $value);
		}

		return $templateMgr->fetch($plugin->getTemplatePath() . 'settings.tpl');
	}

	/**
	 * Assign form data to user-submitted data.
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array(
			'langsci_userWebsiteSettings_path',
			'langsci_userWebsiteSettings_publicProfile',
			'langsci_userWebsiteSettings_email',
			'langsci_userWebsiteSettings_hallOfFame',
			'langsci_userWebsiteSettings_profileImage',
		));
	}

	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$press = $this->getPress();
		$press->updateSetting('langsci_userWebsiteSettings_path', trim($this->getData('langsci_userWebsiteSettings_path'), "\"\';"), 'string');
		$press->updateSetting('langsci_userWebsiteSettings_publicProfile', trim($this->getData('langsci_userWebsiteSettings_publicProfile'), "\"\';"), 'string');
		$press->updateSetting('langsci_userWebsiteSettings_email', trim($this->getData('langsci_userWebsiteSettings_email'), "\"\';"), 'string');
		$press->updateSetting('langsci_userWebsiteSettings_hallOfFame', trim($this->getData('langsci_userWebsiteSettings_hallOfFame'), "\"\';"), 'string');
		$press->updateSetting('langsci_userWebsiteSettings_profileImage', trim($this->getData('langsci_userWebsiteSettings_profileImage'), "\"\';"), 'string');
	}
}
?>
