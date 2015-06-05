<?php

/**
 * @file controllers/grid/form/StaticPageForm.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageForm
 * @ingroup controllers_grid_staticPages
 *
 * Form for press managers to create and modify sidebar blocks
 *
 */

import('lib.pkp.classes.form.Form');

class LocaleFileForm extends Form {
	/** @var int Context (press / journal) ID */
	var $contextId;

	/** @var string Static page name */
	var $filePath;

	var $locale;

	/** @var StaticPagesPlugin Static pages plugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function LocaleFileForm($customLocalePlugin, $contextId, $filePath, $locale) {



		parent::Form($customLocalePlugin->getTemplatePath() . 'test.tpl');
		$this->filePath = $filePath;
		$this->locale = $locale;

		$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
		fwrite($myfile, "\nLocaleFileForm->constr, locale: " . $this->locale);
		fclose($myfile);

		/*$this->contextId = $contextId;
		$this->staticPageId = $staticPageId;*/
		$this->plugin = $customLocalePlugin;

		// Add form checks
	/*	$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidator($this, 'title', 'required', 'plugins.generic.staticPages.nameRequired'));
		$this->addCheck(new FormValidatorRegExp($this, 'path', 'required', 'plugins.generic.staticPages.pathRegEx', '/^[a-zA-Z0-9\/._-]+$/'));
		$this->addCheck(new FormValidatorCustom($this, 'path', 'required', 'plugins.generic.staticPages.duplicatePath', create_function('$path,$form,$staticPagesDao', '$page = $staticPagesDao->getByPath($form->contextId, $path); return !$page || $page->getId()==$form->staticPageId;'), array($this, DAORegistry::getDAO('StaticPagesDAO'))));*/

	}

	/**
	 * Initialize form data from current group group.
	 */
	function initData() {

$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
fwrite($myfile, "\nLFF->initData, filePath: " . $this->filePath);
fclose($myfile);

		$templateMgr = TemplateManager::getManager();
		


			//$this->setData('title', $staticPage->getTitle(null)); // Localized
			$this->setData('title', "asfasfa"); // Localized
		
$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
fwrite($myfile, "\nLFF->initData (ende)");
fclose($myfile);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		//$this->readUserVars(array('path', 'title', 'content'));
	}

	/**
	 * @see Form::fetch
	 */
	function fetch($request) {

		$file =  $this->filePath;		
		$locale = $this->locale;

$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
fwrite($myfile, "\nLFF->fetch, this->filePath: " . $this->filePath);
fwrite($myfile, "\nLFF->fetch, filePath: " . $file);
fwrite($myfile, "\nLFF->fetch, locale: " . $locale);
fclose($myfile);

		$templateMgr =& TemplateManager::getManager();

	//$templateMgr->assign('pluginJavaScriptURL', $this->plugin->getJavaScriptURL($request));

		import('lib.pkp.classes.file.FileManager');
		$fileManager = new FileManager();

		import('lib.pkp.classes.file.EditableLocaleFile');
		$press = Request::getPress();
		$pressId = $press->getId();


		$publicFilesDir = Config::getVar('files', 'public_files_dir');
		$customLocaleDir = $publicFilesDir . DIRECTORY_SEPARATOR . 'presses' . DIRECTORY_SEPARATOR . $pressId . DIRECTORY_SEPARATOR . CUSTOM_LOCALE_DIR;
		$customLocalePath = $customLocaleDir . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $file;

		if ($fileManager->fileExists($customLocalePath)) {
			$localeContents = EditableLocaleFile::load($customLocalePath);

		} else {
			$localeContents = null;
		}

		if (!CustomLocaleAction::isLocaleFile($locale, $file)) {

		} else {

		}


		$referenceLocaleContents = EditableLocaleFile::load($file);
		$referenceLocaleContentsRangeInfo = Handler::getRangeInfo($request,'referenceLocaleContents');

		$templateMgr->assign('filePath', $this->filePath);
		$templateMgr->assign('localeContents', $localeContents);
		$templateMgr->assign('locale', $locale);
import('lib.pkp.classes.core.ArrayItemIterator');

	$templateMgr->assign_by_ref('referenceLocaleContents', new ArrayItemIterator($referenceLocaleContents, $referenceLocaleContentsRangeInfo->getPage(), $referenceLocaleContentsRangeInfo->getCount()));
	
//		$templateMgr->assign('pluginJavaScriptURL', $this->plugin->getJavaScriptURL($request));
/*
		$context = $request->getContext();
		if ($context) $templateMgr->assign('allowedVariables', array(
			'contactName' => __('plugins.generic.tinymce.variables.principalContactName', array('value' => $context->getSetting('contactName'))),
			'contactEmail' => __('plugins.generic.tinymce.variables.principalContactEmail', array('value' => $context->getSetting('contactEmail'))),
			'supportName' => __('plugins.generic.tinymce.variables.supportContactName', array('value' => $context->getSetting('supportName'))),
			'supportPhone' => __('plugins.generic.tinymce.variables.supportContactPhone', array('value' => $context->getSetting('supportPhone'))),
			'supportEmail' => __('plugins.generic.tinymce.variables.supportContactEmail', array('value' => $context->getSetting('supportEmail'))),
		));*/
/*
$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
fwrite($myfile, "\nLFF->fetch (ende)");
fclose($myfile);*/

		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		/*$staticPagesDao = DAORegistry::getDAO('StaticPagesDAO');
		if ($this->staticPageId) {
			// Load and update an existing page
			$staticPage = $staticPagesDao->getById($this->staticPageId, $this->contextId);
		} else {
			// Create a new static page
			$staticPage = $staticPagesDao->newDataObject();
			$staticPage->setContextId($this->contextId);
		}

		$staticPage->setPath($this->getData('path'));
		$staticPage->setTitle($this->getData('title'), null); // Localized
		$staticPage->setContent($this->getData('content'), null); // Localized

		if ($this->staticPageId) {
			$staticPagesDao->updateObject($staticPage);
		} else {
			$staticPagesDao->insertObject($staticPage);
		}*/
	}
}

?>
