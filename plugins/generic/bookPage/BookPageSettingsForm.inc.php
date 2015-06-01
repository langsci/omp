<?php
/**
 * @file plugins/generic/BookPageSettingsForm.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class BookPageSettingsForm
 * @ingroup plugins_blocks_browse
 *
 * @brief Form for press managers to setup Book Page plugin
 */
import('lib.pkp.classes.form.Form');
class BookPageSettingsForm extends Form {
	//
	// Private properties
	//
	/** @var int press ID */
	var $_pressId;
	/** @var BrowseBlockPlugin Browse block plugin */
	var $_plugin;
	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin BookPagePlugin
	 * @param $pressId int
	 */
	function BookPageSettingsForm($plugin, $pressId) {
		$this->setPressId($pressId);
		$this->setPlugin($plugin);
		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');
		$this->addCheck(new FormValidatorPost($this));
		$this->setData('pluginName', $plugin->getName());
		$this->setData('pluginJavaScriptPath', $plugin->getPluginPath());
	}
	//
	// Getters and Setters
	//
	/**
	 * Get the Press ID.
	 * @return int
	 */
	function getPressId() {
		return $this->_pressId;
	}
	/**
	 * Set the Press ID.
	 * @param $pressId int
	 */
	function setPressId($pressId) {
		$this->_pressId = $pressId;
	}
	/**
	 * Get the plugin.
	 * @return BookPagePlugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}
	/**
	 * Set the plugin.
	 * @param $plugin BookPagePlugin
	 */
	function setPlugin($plugin) {
		$this->_plugin = $plugin;
	}
	//
	// Implement template methods from Form
	//
	/**
	 * @see Form::initData()
	 */
	function initData() {
		$pressId = $this->getPressId();
		$plugin = $this->getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($pressId, $fieldName));
		}
	}
	/**
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}
	/**
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$pressId = $this->getPressId();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($pressId, $fieldName, $this->getData($fieldName), $fieldType);
		}
	}
	
	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'selectedLanguage' => 'string',
			'selectedTheme' => 'string',
			'facebook' => 'bool',
			'twitter' => 'bool',
			'googleplus' => 'bool',
			'mail' => 'bool',
			'info' => 'bool',
			'backend' => 'string',
		);
	}
}
?>