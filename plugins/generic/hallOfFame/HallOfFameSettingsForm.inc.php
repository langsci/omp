<?php

/**
 * @file plugins/generic/addThis/AddThisSettingsForm.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AddThisSettingsForm
 * @ingroup plugins_generic_AddThis
 *
 * @brief Form for adding/editing the settings for the AddThis plugin
 */

import('lib.pkp.classes.form.Form');

class HallOfFameSettingsForm extends Form {
	/** @var Press The press associated with the plugin being edited */
	var $_press;

	/** @var Plugin The plugin being edited */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin Plugin
	 * @param $press Press
	 */
	function HallOfFameSettingsForm($plugin, $press) {
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
				'userGroupsHallOfFame' => $press->getSetting('userGroupsHallOfFame'),
				'pathHallOfFame' => $press->getSetting('pathHallOfFame'),
				'onlyPublishedMonographs' => $press->getSetting('onlyPublishedMonographs'),
				'linksToPublicProfile' => $press->getSetting('linksToPublicProfile'),
				'unifiedStyleSheetForLinguistics' => $press->getSetting('unifiedStyleSheetForLinguistics'),
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
			'pathHallOfFame',
			'userGroupsHallOfFame',
			'onlyPublishedMonographs',
			'linksToPublicProfile',
			'unifiedStyleSheetForLinguistics',
		));
	}

	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$press = $this->getPress();

		$press->updateSetting('pathHallOfFame', trim($this->getData('pathHallOfFame'), "\"\';"), 'string');
		$press->updateSetting('userGroupsHallOfFame', trim($this->getData('userGroupsHallOfFame'), "\"\';"), 'string');
		$press->updateSetting('onlyPublishedMonographs', trim($this->getData('onlyPublishedMonographs'), "\"\';"), 'string');
		$press->updateSetting('linksToPublicProfile', trim($this->getData('linksToPublicProfile'), "\"\';"), 'string');
		$press->updateSetting('unifiedStyleSheetForLinguistics', trim($this->getData('unifiedStyleSheetForLinguistics'), "\"\';"), 'string');
	}
}
?>
