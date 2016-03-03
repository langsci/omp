<?php

/**
 * @file plugins/generic/simplifyWorkflow/SimplifyWorkflowPlugin.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SimplifyWorkflowPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.simplifyWorkflow.SimplifyWorkflowDAO');
import('plugins.generic.simplifyWorkflow.SWAboutContextHandler');

class SimplifyWorkflowPlugin extends GenericPlugin {

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path) {

		if (parent::register($category, $path)) {

			if ($this->getEnabled()) {

				// overwrite templates that are called with display or include
				HookRegistry::register ('TemplateManager::display',
						array(&$this, 'handleDisplayTemplate'));
				HookRegistry::register ('TemplateManager::include',

				// overwrite templates that are called with setTemplate
						array(&$this, 'handleIncludeTemplate'));
				HookRegistry::register ('addparticipantform::Constructor',
						array(&$this, 'handleAddParticipantForm')); 
				HookRegistry::register ('submissionfilesuploadform::Constructor', array(&$this, 
									  'handleSubmissionFilesUploadForm'));
				HookRegistry::register ('catalogentrycatalogmetadataform::Constructor',
						array(&$this, 'handleCatalogEntryForm'));
				HookRegistry::register ('catalogentryformatmetadataform::Constructor',
						array(&$this, 'handlePublicationEntryForm'));

				// control database action
				HookRegistry::register ('eventlogdao::_insertobject',
						array(&$this, 'handleInsertObject'));

				// action at the end of the submission process
				HookRegistry::register('submissionsubmitstep3form::validate', array(&$this, 'handleAssignEditors'));

				// delete notifications when loading dashboard page -> find better solution!!!
				HookRegistry::register ('LoadHandler', array(&$this, 'handleOnLoadDeleteNotifications'));

			}
			return true;
		}
		return false;
	}

	function handleAddParticipantForm($hookName, $args)  {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'coreAddParticipantFormModified.tpl'); 

		return true;

	}

	function handleSubmissionFilesUploadForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'fileUploadFormModified.tpl'); 

		return true;
	}


	function handleCatalogEntryForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'catalogMetadataFormFieldsModified.tpl'); 

		return true;
	}

	function handlePublicationEntryForm($hookName, $args) {

		$form =& $args[0]; 

		$form->setTemplate($this->getTemplatePath() . 
				'publicationMetadataFormFieldsModified.tpl'); 

		return true;
	}


	function handleIncludeTemplate($hookName, $args) {

		$templateMgr =& $args[0];
		$params =& $args[1];

		if (!isset($params['smarty_include_tpl_file'])) return false;
		switch ($params['smarty_include_tpl_file']) {
			case 'core:submission/form/step1.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'coreStep1Modified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'core:submission/form/step3.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'coreStep3Modified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'core:submission/submissionMetadataFormFields.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'coreSubmissionMetadataFormFieldsModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'submission/form/categories.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'categoriesModified.tpl', 'text/html', 'TemplateManager::include');
				return true;
			case 'submission/form/series.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'seriesModified.tpl', 'text/html', 'TemplateManager::include');
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
				'coreSubmissionModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'workflow/editorial.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'coreEditorialModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'workflow/production.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'productionModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'authorDashboard/authorDashboard.tpl':
				$templateMgr->display($this->getTemplatePath() . 
				'authorDashboardModified.tpl', 'text/html', 'TemplateManager::display');
				return true;
			case 'about/editorialPolicies.tpl':
				$context = $this->getRequest()->getContext();
				$templateMgr->assign('submissionInfo', SWAboutContextHandler::getSubmissionsInfo($context));
				$templateMgr->display($this->getTemplatePath() . 'editorialPoliciesModified.tpl', 'text/html', 'TemplateManager::display');
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

	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

}

?>
