<?php

/**
 * @file plugins/generic/annotations/AnnotationsPlugin.inc.php
 *
 * Copyright (c) 2014 Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AnnotationsPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');


class AnnotationsPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.annotations.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.annotations.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {

		if (parent::register($category, $path)) {

			if ($this->getEnabled()) {

				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));
			}
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
		$tail =& implode($request->getRequestedArgs());

		if ($tail=="file") {
			$op = "file";
		}
	
		if ($page=="management" && in_array($op,
				  array('annotations', 
						'file'))) {

			define('HANDLER_CLASS', 'AnnotationsHandler');
			define('ANNOTATIONS_PLUGIN_NAME', $this->getName());

			$this->import('AnnotationsHandler');

		return true;

		}

	}

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}
}

?>
