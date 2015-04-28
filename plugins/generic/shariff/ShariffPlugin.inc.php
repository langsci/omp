<?php

/**
 * @file plugins/generic/shariff/ShariffPlugin.inc.php
 *
 * Copyright (c) 2014 CeDiS, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class shariffPlugin
 * @ingroup plugins_generic_langsci
 *
 * @brief langsci plugin for all langsci changes
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class ShariffPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.shariff.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.shariff.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				/* call hook in footer */
				HookRegistry::register ('Templates::Common::Footer::PageFooter', array(&$this, 'addShariffButtons'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Hook callback: Handle requests.
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function addShariffButtons($hookName, $args) {
		$template =& $args[1];
		$output =& $args[2];
		
		$output .= '
		<link rel="stylesheet" href="'. Request::getBaseUrl() .'/'. $this->getPluginPath().'/css/shariff.min.css" type="text/css" />
		<div class="shariff" data-lang="en" data-services="[&quot;twitter&quot;,&quot;facebook&quot;,&quot;googleplus&quot;,&quot;flattr&quot;]"  data-backend-url="/shariff-backend/" data-url="http://test.langsci-press.org"></div> <script src="'. Request::getBaseUrl() .'/'. $this->getPluginPath().'/shariff.complete.js"></script>';
		
		return false;
	}
	
	/**
	 * Get the name and the path of the css file.
	 * @return string
	 */
	function getStyleSheet(){
		return $this->getPluginPath() . '/css/shariff.min.css';
	}
	
	
	/**
	 * Get the name of the settings file to be installed on new press
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

}

?>
