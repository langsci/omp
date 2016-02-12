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

class IdentificationCodesPlugin extends GenericPlugin {


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

		// get url path components to overwrite them 
		$pageUrl =& $args[0];
		$opUrl =& $args[1];

		if ($pageUrl=="tools" && $opUrl=="identificationCodes") {

			define('HANDLER_CLASS', 'IdentificationCodesHandler');
			define('IDENTIFICATIONCODES_PLUGIN_NAME', $this->getName());

			$this->import('IdentificationCodesHandler');

			return true;
		}
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.identificationCodes.displayName');
	}

	function getDescription() {
		return __('plugins.generic.identificationCodes.description');
	}

	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
