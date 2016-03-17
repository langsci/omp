<?php

/**
 * @file plugins/generic/identificationCodes/IdentificationCodesSettingsForm.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class IdentificationCodesSettingsForm
 *
 */

import('lib.pkp.classes.form.Form');

class IdentificationCodesSettingsForm extends Form {

	/** @var Press The press associated with the plugin being edited */
	var $_press;

	/** @var Plugin The plugin being edited */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin Plugin
	 * @param $press Press
	 */
	function IdentificationCodesSettingsForm($plugin, $press) {
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
	 * @return Plugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}

	/**
	 * Set the plugin.
	 * @param $plugin
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
				'langsci_identificationCodes_codes' => $press->getSetting('langsci_identificationCodes_codes'),
				'langsci_identificationCodes_display' => $press->getSetting('langsci_identificationCodes_display')

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
			'langsci_identificationCodes_codes',
			'langsci_identificationCodes_display'
		));
	}

	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$press = $this->getPress();

		$press->updateSetting('langsci_identificationCodes_codes', trim($this->getData('langsci_identificationCodes_codes'), "\"\';"), 'string');
		$press->updateSetting('langsci_identificationCodes_display', trim($this->getData('langsci_identificationCodes_display'), "\"\';"), 'string');
	}
}

?>



