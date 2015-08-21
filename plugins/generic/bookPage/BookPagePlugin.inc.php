<?php

/**
 * @file plugins/generic/bookPage/BookPagePlugin.inc.php
 *
 * Copyright (c) 2014 CeDiS, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class bookPagePlugin
 * @ingroup plugins_generic_langsci
 *
 * @brief langsci plugin to add statistic images at the download tab
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class BookPagePlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.bookPage.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.bookPage.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				HookRegistry::register ('TemplateManager::display', array(&$this, 'handleTemplateDisplay'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Overwrite the bookSpecs and bookInfo templates
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleReaderTemplateInclude($hookName, $args) {
		$templateMgr =& $args[0];
		$params =& $args[1];
		$templateMgr->assign_by_ref('base_url', Config::getVar('general','base_url'));
		
		// Hardcover softcover links from catalog entry tag plugin
		import('plugins.generic.catalogEntryTab.CatalogEntryTabDAO');
		$catalogEntryTabDao = new CatalogEntryTabDAO();
		DAORegistry::registerDAO('CatalogEntryTabDAO', $catalogEntryTabDao);		
		
		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'catalog/book/bookInfo.tpl':
			
				// variables for vg wort and hardcover/softcover
				$publishedMonograph = $templateMgr->get_template_vars('publishedMonograph'); // get variable publishedMonograph from template 
				$contextId = $publishedMonograph->getContextId(); 
				$publishedMonographId = $publishedMonograph->getId();
				
				$request = $this->getRequest();
				$baseUrl = $request->getBaseUrl();
				$pluginPath = $this->getPluginPath();
				
				// TODO: not working, dont know why
				// statistics: is there a statistic image of this book? statImageExists as variable given to the template 
				$templateMgr->assign('statImageExists', file_exists($baseUrl.'/'.$pluginPath.'/img/'.$publishedMonographId.'.png'));
				
				
				// get names of the publication formats from settings
				$bookPagePlugin = PluginRegistry::getPlugin('generic', 'bookpageplugin');
				$templateMgr->assign('bookPageForthcoming', $bookPagePlugin->getSetting($contextId, 'bookPageForthcoming'));
				$templateMgr->assign('bookPageReview', $bookPagePlugin->getSetting($contextId, 'bookPageReview'));
				$templateMgr->assign('bookPageDownload', $bookPagePlugin->getSetting($contextId, 'bookPageDownload'));
				
				// hardcover/softcover: get hardcover softcover links of this book and give them as variables to the template
				$templateMgr->assign('softcoverLink', $catalogEntryTabDao->getLink($publishedMonographId,"softcover"));
				$templateMgr->assign('hardcoverLink', $catalogEntryTabDao->getLink($publishedMonographId,"hardcover"));
				
				if(null!==($catalogEntryTabDao->getLink($publishedMonographId,"openreview0"))){
					$templateMgr->assign('openreviewLink0', $catalogEntryTabDao->getLink($publishedMonographId,"openreview0"));
				}
				if(null!==($catalogEntryTabDao->getLink($publishedMonographId,"openreview1"))){
					$templateMgr->assign('openreviewLink1', $catalogEntryTabDao->getLink($publishedMonographId,"openreview1"));
				}
				
				// generate imageUrl for VG Wort and save it as template variable
				$templateMgr->assign('imageUrl', $this->createVgWortUrl($contextId, $publishedMonographId));
				
				// plugin path as variable given to the template to overwrite bookFiles.tpl
				$templateMgr->assign('pluginPath', $pluginPath);
				
				// get publication formats that shall be excluded from vg wort counting out of VG wort plugin settings
				$vgWortPlugin = PluginRegistry::getPlugin('generic', 'vgwortplugin');
				$excludedPubFormats = $vgWortPlugin->getSetting($contextId, 'vgWortExcludedPubFormats');
				$excludedPubFormatsArray = explode(',', $excludedPubFormats);
				$templateMgr->assign('excludedPubFormats', $excludedPubFormatsArray);
				
				// call template
				$templateMgr->display($this->getTemplatePath() . 'langsciBookInfo.tpl', 'text/html', 'TemplateManager::include');
				
				return true;
		}
		return false;
	}

	
	/**
	 * Create the url for the vg wort pixel image with the domain and the public code
	 * @param $contextId int The id of the press
	 * @param $publishedMonographId int The id of the book
	 * @return $imageUrl string The url of the vg wort pixel image 
	 */
	function createVgWortUrl($contextId, $publishedMonographId){
		
			
				// get the assigned pixel tag of the book
				$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
				$pixelTagObject = $pixelTagDao->getPixelTagBySubmissionId($contextId, $publishedMonographId);
				
				if($pixelTagObject){
					
					$pixelTag = $pixelTagDao->getPixelTag($pixelTagObject->getId());
					
					// create url
					$imageUrl = 'http://' . $pixelTag->getDomain() . '/na/' . $pixelTag->getPublicCode();
					
					return $imageUrl;
					
				}else return '';
				
				
		
	}
	
	

	/**
	 * Hook callback: Handle requests.
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function handleTemplateDisplay($hookName, $args) {
		$templateMgr =& $args[0];
		$template =& $args[1];

		switch ($template) {
			case 'catalog/book/book.tpl':
				HookRegistry::register ('TemplateManager::include', array(&$this, 'handleReaderTemplateInclude'));
				break;
		}
		return false;
	}
	
	/***
	FUNCTIONS FOR SETTINGS
	*/
	
		/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.bookPage.manager.settings'));
		}
		return $verbs;
	}

 	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		if (!parent::manage($verb, $args, $message, $messageParams, $pluginModalContent)) return false;

		switch ($verb) {
			case 'settings':
				$request = $this->getRequest();
				$router = $request->getRouter();
				$context = $router->getContext($request);
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->register_function('plugin_url', array($this, 'smartyPluginUrl'));

				$this->import('BookPageSettingsForm');
				$form = new BookPageSettingsForm($this, $context->getId());
				if (Request::getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						$message = NOTIFICATION_TYPE_SUCCESS;
						return false;
					} else {
						$pluginModalContent = $form->fetch($request);
					}
					return $json->getString();
				} else {
					$form->initData();
					$pluginModalContent = $form->fetch($request);
				}
				return true;
			default:
				// Unknown management verb
				assert(false);
				return false;
		}
	}

	/**
	 * Define management link actions for the settings verb.
	 * @return LinkAction
	 */
	function getManagementVerbLinkAction($request, $verb) {
		$router = $request->getRouter();

		list($verbName, $verbLocalized) = $verb;

		if ($verbName === 'settings') {
			import('lib.pkp.classes.linkAction.request.AjaxLegacyPluginModal');
			$actionRequest = new AjaxLegacyPluginModal(
					$router->url($request, null, null, 'plugin', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
					$this->getDisplayName()
			);
			return new LinkAction($verbName, $actionRequest, $verbLocalized, null);
		}

		return null;
	}
	

	/**
	 * Get the name of the settings file to be installed on new press
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . '/templates/';
	}


}

?>
