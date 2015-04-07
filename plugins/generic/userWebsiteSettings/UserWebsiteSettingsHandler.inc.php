<?php

/**
 * @file UserWebsiteSettingsHandler.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserWebsiteSettingsHandler
 * Find page content and display it.
 */

import('classes.handler.Handler');
import('plugins.generic.userWebsiteSettings.UserWebsiteSettingsDAO');

class UserWebsiteSettingsHandler extends Handler {

	/** @var StaticPagesPlugin The static pages plugin */
	static $plugin;

	/**
	 * Constructor
	 */
	function UserWebsiteSettingsHandler() {
		parent::Handler();
	}

	function viewUserWebsiteSettings($args, $request) {

		// redirect to index page if no user is logged in
		$user = $request->getUser();
		if (!$user) {
			$request->redirect('index');
		}

		// if there is no table "langsci_website_settings" redirect to index page
		$userWebsiteSettingsDAO = new UserWebsiteSettingsDAO;
		if(!$userWebsiteSettingsDAO->existsTable('langsci_website_settings')) {
			$request->redirect('index');
		}

		// get username and user_id
		$username = $user-> getUsername();
		$userId = $user->getId();

		// get the settings for this plugin 
		$press = $request -> getPress();
		$settings = array(
			"PublicProfile"		=> $press->getSetting('langsci_userWebsiteSettings_publicProfile'),
			"Email"					=> $press->getSetting('langsci_userWebsiteSettings_email'),
			"HallOfFame"			=> $press->getSetting('langsci_userWebsiteSettings_hallOfFame'),
			"ProfileImage"			=> $press->getSetting('langsci_userWebsiteSettings_profileImage')
		);

		// set up template manager and assign setting variables
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle','plugins.generic.userWebsiteSettings.title');
		$templateMgr->assign('pressPath',$this->getPressPath($request));
		$templateMgr->assign('userId',$userId);
		$templateMgr->assign('pathHallOfFame',$press->getSetting('langsci_hallOfFame_path'));
		$templateMgr->assign('pathPublicProfiles',$press->getSetting('langsci_publicProfiles_path'));

		foreach($settings as $key => $value) {
			$templateMgr->assign(''.$key, $value);
		}

		// if save button is set: get data from website and save it to database, else: get data from the database
		foreach($settings as $key => $value) {
			if ($value && isset($_POST['buttonSaveWebsiteSettings'])) {
				$userWebsiteSettingsDAO -> setWebsiteSetting($userId,$key,$_POST['checkbox'.$key]);
				$templateMgr->assign('issetCheckbox'.$key, $userWebsiteSettingsDAO -> getWebsiteSetting($userId,$key)=='true');
			} else if ($value) {

				$templateMgr->assign('issetCheckbox'.$key, $userWebsiteSettingsDAO -> getWebsiteSetting($userId,$key)=='true');
			}
		}	

		$userWebsiteSettingsPlugin = PluginRegistry::getPlugin('generic', USERWEBSITESETTINGS_PLUGIN_NAME);
		$templateMgr->display($userWebsiteSettingsPlugin->getTemplatePath()."userWebsiteSettings.tpl");
	}

	function uploadProfileImage($args, $request){

		//todo: darf nicht direkt aufgerufen werden (url)

		// redirect to index page if no user is logged in 
		$user = $request->getUser();
		if (!$user) {
			$request->redirect('index');
		}

		// get username and user_id
		$userWebsiteSettingsDAO = new UserWebsiteSettingsDAO;
		$username = $user-> getUsername();	
		$userId = $user->getId();

		// get image path
		$baseUrl = $request->getBaseUrl();
		$imagePath = $baseUrl . "/plugins/generic/userWebsiteSettings/profileImg/".$userId .".jpg";

		if(!@getimagesize($imagePath)){
			$imagePath = $baseUrl . "/plugins/generic/userWebsiteSettings/profileImg/noImage.jpg";
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.websiteSettings');
		$templateMgr->assign('userId', $userId);
		$templateMgr->assign('imagePath', $imagePath);
		$templateMgr->assign('baseUrl',$baseUrl);	

		$userWebsiteSettingsPlugin = PluginRegistry::getPlugin('generic', USERWEBSITESETTINGS_PLUGIN_NAME);
		$templateMgr->display($userWebsiteSettingsPlugin->getTemplatePath()."uploadProfileImage.tpl");

	}

	function getPressPath(&$request) {
		$press = $request -> getPress();
		$pressPath = $press -> getPath();
 		$completeUrl = $request->getCompleteUrl();
		return substr($completeUrl,0,strpos($completeUrl,$pressPath)) . $pressPath ;
	}


}

?>

