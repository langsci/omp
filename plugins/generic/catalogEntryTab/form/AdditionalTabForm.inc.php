<?php

/**
 * @file controllers/tab/catalogEntry/form/CatalogEntryCatalogMetadataForm.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CatalogEntryCatalogMetadataForm
 * @ingroup controllers_tab_catalogEntry_form_CatalogEntryCatalogMetadataForm
 *
 * @brief Displays a submission's catalog metadata entry form.
 */

import('lib.pkp.classes.form.Form');

class AdditionalTabForm extends Form {

	/** @var $_monograph Monograph The monograph used to show metadata information */
	var $_monograph;

	/** @var $_publishedMonograph PublishedMonograph The published monograph associated with this monograph */
	var $_publishedMonograph;

	/** @var $_stageId int The current stage id */
	var $_stageId;

	/** @var $_userId int The current user ID */
	var $_userId;

	/** @var $_stageId int The current stage id */
	var $_tab;

	/** @var $_userId int The current user ID */
	var $_tabPos;

	/**
	 * Parameters to configure the form template.
	 */
	var $_formParams;

	/**
	 * Constructor.
	 * @param $monographId integer
	 * @param $userId integer
	 * @param $stageId integer
	 * @param $formParams array
	 */
	function AdditionalTabForm($monographId, $userId, $stageId = null, $formParams = null) {

		parent::Form('../plugins/generic/catalogEntryTab/templates/tabContent.tpl');

		$monographDao = DAORegistry::getDAO('MonographDAO');
		$this->_monograph = $monographDao->getById($monographId);

		$this->_stageId = $stageId;
		$this->_userId = $userId;
		$this->_formParams = $formParams;

	}

	/**
	 * Fetch the HTML contents of the form.
	 * @param $request PKPRequest
	 * return string
	 */
	function fetch($request) {

		$monograph = $this->getMonograph();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('submissionId', $this->getMonograph()->getId());
		$templateMgr->assign('stageId', $this->getStageId());
		$templateMgr->assign('tab', $this->getTab());
		$templateMgr->assign('tabPos', $this->getTabPos());
		$templateMgr->assign('formParams', $this->getFormParams());

		$catalogEntryTabDAO = DAORegistry::getDAO('CatalogEntryTabDAO');
		$templateMgr->assign('softcoverlink', $catalogEntryTabDAO->getLink($this->getMonograph()->getId(),1));
		$templateMgr->assign('hardcoverlink', $catalogEntryTabDAO->getLink($this->getMonograph()->getId(),0));

		return parent::fetch($request);
	}

	function initData($args,$request) {

		$this->_tab = $args['tab'];
		$this->_tabPos = $args['tabPos'];

		$monograph = $this->getMonograph();
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
		$this->_publishedMonograph = $publishedMonographDao->getById($monograph->getId(), null, false);
	}


	//
	// Getters and Setters
	//
	/**
	 * Get the Monograph
	 * @return Monograph
	 */
	function &getMonograph() {
		return $this->_monograph;
	}

	/**
	 * Get the PublishedMonograph
	 * @return PublishedMonograph
	 */
	function &getPublishedMonograph() {
		return $this->_publishedMonograph;
	}

	/**
	 * Get the stage id
	 * @return int
	 */
	function getStageId() {
		return $this->_stageId;
	}


	function getTab() {
		return $this->_tab;
	}

	function getTabPos() {
		return $this->_tabPos;
	}

	/**
	 * Get the extra form parameters.
	 */
	function getFormParams() {
		return $this->_formParams;
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$vars = array(
			 'softcoverlink','hardcoverlink',// Cover image
		);

		$this->readUserVars($vars);
	}

	/**
	 * Validate the form.
	 * @return boolean
	 */
	function validate() {

		return parent::validate();
	}

	/**
	 * Save the metadata and store the catalog data for this published
	 * monograph.
	 */
	function execute($request) {

		parent::execute();

		$monograph = $this->getMonograph();

		$catalogEntryTabDAO = DAORegistry::getDAO('CatalogEntryTabDAO');

		$catalogEntryTabDAO->setLink($monograph->getId(),1,$this->getData('softcoverlink'));
		$catalogEntryTabDAO->setLink($monograph->getId(),0,$this->getData('hardcoverlink'));

//		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
	//	$publishedMonograph = $publishedMonographDao->getById($monograph->getId(), null, false); /* @var $publishedMonograph PublishedMonograph */
		/*$isExistingEntry = $publishedMonograph?true:false;
		if (!$publishedMonograph) {
			$publishedMonograph = $publishedMonographDao->newDataObject();
			$publishedMonograph->setId($monograph->getId());
		}*/

	}

}

?>
