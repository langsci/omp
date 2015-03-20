<?php

/**
 * @file plugins/themes/langSci/LangSciThemePlugin.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LangSciThemePlugin
 * @ingroup plugins_themes_langSci
 *
 * @brief LangSci theme
 */

import('lib.pkp.classes.plugins.ThemePlugin');

class LangSciThemePlugin extends ThemePlugin {
	/**
	 * Constructor
	 */
	function LangSciThemePlugin() {
		parent::ThemePlugin();
	}

	/**
	 * Get the name of the settings file to be installed on new journal
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the name of the settings file to be installed site-wide when
	 * OJS is installed.
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.themes.langSci.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('plugins.themes.langSci.description');
	}

	/**
	 * @see ThemePlugin::getLessStylesheet
	 */
	function getLessStylesheet() {
		return 'index.less';
	}
}

?>
