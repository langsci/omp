<?php

/**
 * @file plugins/generic/registerPage/RegisterPagePlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
 *
 * @class RegisterPagePlugin
 *
 * @brief class 
 */


import('lib.pkp.classes.plugins.GenericPlugin');

class RegisterPagePlugin extends GenericPlugin {


	function register($category, $path) {
			
		
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleRegisterPageRequest'));
				HookRegistry::register ('TemplateManager::display', array(&$this, 'handleDisplayTemplate'));
			}
			return true;
		}
		return false;
	}

	function handleRegisterPageRequest($hookName, $args) {
error_reporting(E_ALL);
ini_set('display_errors', true);
		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page=='user' && in_array($op,
				  array('register', 
						'validation',
						'verifyEmail',
						'saveSubscriptions',
						'subscriptions'))) {

			define('REGISTERPAGE_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'RegisterPageHandler');

			$this->import('RegisterPageHandler');
			return true;
		}
		return false;
	}

	function handleDisplayTemplate($hookName, $args) {

		$templateMgr =& $args[0];
		$template =& $args[1];

		switch ($template) {

			case 'user/profile.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/profileModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
		}
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.registerPage.displayName');
	}

	function getDescription() {
		return __('plugins.generic.registerPage.description');
	}

}

?>
