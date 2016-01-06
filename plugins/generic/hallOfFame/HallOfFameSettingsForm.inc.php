<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameSettingsForm.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameSettingsForm
 * 
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
				'langsci_hallOfFame_userGroups' => $press->getSetting('langsci_hallOfFame_userGroups'),
				'langsci_hallOfFame_path' => $press->getSetting('langsci_hallOfFame_path'),
				'langsci_hallOfFame_recentDate' => $press->getSetting('langsci_hallOfFame_recentDate'),
				'langsci_hallOfFame_linksToPublicProfile' => $press->getSetting('langsci_hallOfFame_linksToPublicProfile'),
				'langsci_hallOfFame_unifiedStyleSheetForLinguistics' => $press->getSetting('langsci_hallOfFame_unifiedStyleSheetForLinguistics'),
				'langsci_hallOfFame_startCounting' => $press->getSetting('langsci_hallOfFame_startCounting'),
				'langsci_hallOfFame_path' => $press->getSetting('langsci_hallOfFame_path'),
				'langsci_hallOfFame_percentileRanks' => $press->getSetting('langsci_hallOfFame_percentileRanks'),
				'langsci_hallOfFame_minNumberOfSeries' => $press->getSetting('langsci_hallOfFame_minNumberOfSeries'),
				'langsci_hallOfFame_medalCount' => $press->getSetting('langsci_hallOfFame_medalCount'),
				'langsci_hallOfFame_includeCommentators' => $press->getSetting('langsci_hallOfFame_includeCommentators'),
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
			'langsci_hallOfFame_path',
			'langsci_hallOfFame_userGroups',
			'langsci_hallOfFame_recentDate',
			'langsci_hallOfFame_linksToPublicProfile',
			'langsci_hallOfFame_unifiedStyleSheetForLinguistics',
			'langsci_hallOfFame_startCounting',
			'langsci_hallOfFame_percentileRanks',
			'langsci_hallOfFame_minNumberOfSeries',
			'langsci_hallOfFame_medalCount',
			'langsci_hallOfFame_includeCommentators',
		));
	}



	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$press = $this->getPress();

		$press->updateSetting('langsci_hallOfFame_path', trim($this->getData('langsci_hallOfFame_path'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_userGroups', trim($this->getData('langsci_hallOfFame_userGroups'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_recentDate', trim($this->getData('langsci_hallOfFame_recentDate'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_linksToPublicProfile', trim($this->getData('langsci_hallOfFame_linksToPublicProfile'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_unifiedStyleSheetForLinguistics', trim($this->getData('langsci_hallOfFame_unifiedStyleSheetForLinguistics'), "\"\';"), 'string');

		$press->updateSetting('langsci_hallOfFame_startCounting', trim($this->getData('langsci_hallOfFame_startCounting'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_percentileRanks', trim($this->getData('langsci_hallOfFame_percentileRanks'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_minNumberOfSeries', trim($this->getData('langsci_hallOfFame_minNumberOfSeries'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_medalCount', trim($this->getData('langsci_hallOfFame_medalCount'), "\"\';"), 'string');
		$press->updateSetting('langsci_hallOfFame_includeCommentators', trim($this->getData('langsci_hallOfFame_includeCommentators'), "\"\';"), 'string');
	}

}
?>
