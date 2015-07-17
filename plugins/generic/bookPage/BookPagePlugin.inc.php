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
		
		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'catalog/book/bookInfo.tpl':
				
				// VG Wort Pixel Zeugs
				
				// variables 
				$publishedMonograph = $templateMgr->get_template_vars('publishedMonograph'); // get variable publishedMonograph from template 
				$contextId = $publishedMonograph->getContextId(); 
				$publishedMonographId = $publishedMonograph->getId();
			
				// imageUrl
				$imageUrl = $this->createVgWortUrl($contextId, $publishedMonographId);
				//echo("imageUrl: ".$imageUrl."\n");
				
				$templateMgr->assign('imageUrl', $imageUrl);
				
				// end of VG Wort Zeugs
				
				// plugin path as variable given to the template to overwrite bookFiles.tpl
				$templateMgr->assign('pluginPath', $this->getPluginPath());
				
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
	 * @copydoc PKPPlugin::getManagementVerbs()
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.bookPage.settings'));
		}
		return $verbs;
	}
	
	/**
	 * Define management link actions for the settings verb.
	 * @param $request PKPRequest
	 * @param $verb string
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
	 * @copydoc PKPPlugin::manage()
	 */
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		$request = $this->getRequest();
		$press = $request->getPress();
		$templateMgr = TemplateManager::getManager($request);

		switch ($verb) {

			case 'settings':
					$this->import('BookPageSettingsForm');
					$form = new ShariffSettingsForm($this, $press);
					if ($request->getUserVar('save')) {
						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.generic.bookPage.form.saved'));
							return false;
						} else {
							$pluginModalContent = $form->fetch($request);
						}
					} else {
						$form->initData();
						$pluginModalContent = $form->fetch($request);
					}

				return true;


			default:
				// let the parent handle it.
				return parent::manage($verb, $args, $message, $messageParams);
		}
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
		return parent::getTemplatePath() . '/';
	}


}

?>
