<?php

/**
 * @file plugins/generic/simplifyWorkflow/SimplifyWorkflowPlugin.inc.php
 *
 * Copyright (c) 2014 Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SimplifyWorkflowPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.simplifyWorkflow.SimplifyWorkflowDAO');

class SimplifyWorkflowPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.simplifyWorkflow.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.simplifyWorkflow.description');
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {

		if (parent::register($category, $path)) {

			if ($this->getEnabled()) {
				$locale = AppLocale::getLocale();
				$localeFiles = AppLocale::getLocaleFiles($locale);

				HookRegistry::register('PKPLocale::registerLocaleFile', array(&$this, 'addAsTopLocale'));

				HookRegistry::register ('TemplateManager::display',
						array(&$this, 'handleDisplayTemplate'));
				HookRegistry::register ('TemplateManager::include',

				// set template
						array(&$this, 'handleIncludeTemplate'));
				HookRegistry::register ('addparticipantform::Constructor',
						array(&$this, 'handleAddParticipantForm')); 
				HookRegistry::register ('submissionfilesuploadform::Constructor', array(&$this, 
									  'handleSubmissionFilesUploadForm'));
				HookRegistry::register ('catalogentrycatalogmetadataform::Constructor',
						array(&$this, 'handleCatalogEntryForm'));
				HookRegistry::register ('catalogentryformatmetadataform::Constructor',
						array(&$this, 'handlePublicationEntryForm'));


				HookRegistry::register ('eventlogdao::_insertobject',
						array(&$this, 'handleInsertObject'));
				HookRegistry::register('submissionsubmitstep3form::validate', array(&$this, 'handleAssignEditors'));
				HookRegistry::register ('LoadHandler', array(&$this, 'handleOnLoadDeleteNotifications'));

			}
			return true;
		}
		return false;
	}


/*
$file = fopen("test.txt","a");
fwrite($file,"\r\n include hook: ");
fclose($file);*/


	/* -> PKPLocale.inc.php, static function registerLocaleFile ($locale, $filename, $addToTop = false) */
	/* workaround: warte bis zum letzten locale file und füge dann den localefile von simplifyWorkflow hinzug, 
	   kann u.U. noch besser gelöst werden */
	function addAsTopLocale($hookName, $args) {

		$locale =& $args[0];
		$localeFilename =& $args[1];
 
		if ($localeFilename=="lib/pkp/locale/en_US/submission.xml" ||
			$localeFilename=="locale/en_US/submission.xml" ||
			$localeFilename=="locale/en_US/locale.xml" ||
			$localeFilename=="lib/pkp/locale/en_US/user.xml" ||
			$localeFilename=="lib/pkp/locale/en_US/common.xml" ||
			$localeFilename=="lib/pkp/locale/en_US/grid.xml" ||
			$localeFilename=="lib/pkp/locale/en_US/editor.xml" ||
			$localeFilename=="locale/en_US/editor.xml") {
			AppLocale::registerLocaleFile($locale, "plugins/generic/simplifyWorkflow/locale/en_US/locale.xml");
		}
	}

	function handleAddParticipantForm($hookName, $args)  {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'templates/coreAddParticipantFormModified.tpl'); 
		return true;

	}

	function handleSubmissionFilesUploadForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'templates/fileUploadFormModified.tpl'); 

		return true;
	}


	function handleCatalogEntryForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'templates/catalogMetadataFormFieldsModified.tpl'); 
		return true;
	}

	function handlePublicationEntryForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'templates/publicationMetadataFormFieldsModified.tpl'); 

		return true;
	}


	function handleIncludeTemplate($hookName, $args) {

		$templateMgr =& $args[0];
		$params =& $args[1];

		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'core:submission/form/step1.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/coreStep1Modified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'core:submission/form/step3.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/coreStep3Modified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'core:submission/submissionMetadataFormFields.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/coreSubmissionMetadataFormFieldsModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'submission/form/categories.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/categoriesModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'submission/form/series.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/seriesModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
		}
		return false;
	}


	function handleDisplayTemplate($hookName, $args) {

		$templateMgr =& $args[0];
		$template =& $args[1];

		switch ($template) {

			case 'workflow/submission.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/coreSubmissionModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'workflow/editorial.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/coreEditorialModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'workflow/production.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/productionModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'authorDashboard/authorDashboard.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'templates/authorDashboardModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
		}
		return false;
	}

	function handleInsertObject($hookName, $args) {

		$sql =& $args[0]; 
		$parameters =& $args[1];
		$message = $parameters[5];

		if ($message=='submission.event.fileUploaded') {

			$simplifyWorkflowDAO = new SimplifyWorkflowDAO;
			$simplifyWorkflowDAO->setTermsToOpenAcess();
		}
		return true;
	}

	function handleAssignEditors($hookName, $args) {

		$submission_id = $args[0]->submissionId;

		$simplifyWorkflowDAO = new SimplifyWorkflowDAO;
 		$pressManagerId = $simplifyWorkflowDAO->getRoleId("Press Manager");
 		$seriesEditorId = $simplifyWorkflowDAO->getRoleId("Series Editor");

		$seriesEditors = $simplifyWorkflowDAO->getSeriesEditors($submission_id);
		$pressManagers = $simplifyWorkflowDAO->getPressManagers();
 
		// assign press editors (prefer: Sebastian Nordhoff)
		$idPrimPM = 86;

		if (in_array($idPrimPM,$pressManagers)) {
			$simplifyWorkflowDAO->assignParticipant($submission_id,$pressManagerId,$idPrimPM);
		} else {
			if (sizeof($pressManagers)>0) {
				$simplifyWorkflowDAO->assignParticipant($submission_id,$pressManagerId,$pressManagers[0]);
			}
		}

		// assign series editors
		for ($i=0; $i<sizeof($seriesEditors); $i++) {
			$simplifyWorkflowDAO->assignParticipant($submission_id,$seriesEditorId,$seriesEditors[$i]);
		}
	
		// add standard values: publication format: names, digital, physical_format, composition code:00
		// entry key: DA, ... ,rights: CC-BY
		$simplifyWorkflowDAO->addStandardValuesAfterSubmit($submission_id);


		return false;
	}

	function handleOnLoadDeleteNotifications($hookName, $args) {

		$page = $args[0];
		if ($page=="dashboard") {
			$simplifyWorkflowDAO = new SimplifyWorkflowDAO;
			$simplifyWorkflowDAO->deleteNotificationAssignEditor();
		}
		return false;
	}


}

?>
