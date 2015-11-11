<?php

/**
 * @file classes/plugins/PubIdPluginHelper.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PubIdPluginHelper
 * @ingroup plugins
 *
 * @brief Helper class for public identifiers plugins
 */


class PubIdPluginHelper {

	/**
	 * Validate the additional form fields from public identifier plugins.
	 * @param $pressId int
	 * @param $form object CatalogEntryFormatMetadataForm
	 * @param $pubObject object A PublicationFormat object
	 */
	function validate($pressId, &$form, &$pubObject) {
		$pubIdPlugins =& PluginRegistry::loadCategory('pubIds', true);
		if (is_array($pubIdPlugins)) {
			foreach ($pubIdPlugins as $pubIdPlugin) {
				$fieldNames = $pubIdPlugin->getFormFieldNames();
				foreach ($fieldNames as $fieldName) {
					$fieldValue = $form->getData($fieldName);
					$errorMsg = '';
					if(!$pubIdPlugin->verifyData($fieldName, $fieldValue, $pubObject, $pressId, $errorMsg)) {
						$form->addError($fieldName, $errorMsg);
					}
				}
			}
		}
	}

	/**
	 * Init the additional form fields from public identifier plugins.
	 * @param $form object CatalogEntryFormatMetadataForm
	 * @param $pubObject object A PublicationFormat object
	 */
	function init(&$form, &$pubObject) {
		$pubIdPlugins =& PluginRegistry::loadCategory('pubIds', true);
		if (is_array($pubIdPlugins)) {
			foreach ($pubIdPlugins as $pubIdPlugin) {
				$fieldNames = $pubIdPlugin->getFormFieldNames();
				foreach ($fieldNames as $fieldName) {
					$form->setData($fieldName, $pubObject->getData($fieldName));
				}
			}
		}
	}

	/**
	 * Read the additional input data from public identifier plugins.
	 * @param $form object CatalogEntryFormatMetadataForm
	 */
	function readInputData(&$form) {
		$pubIdPlugins =& PluginRegistry::loadCategory('pubIds', true);
		if (is_array($pubIdPlugins)) {
			foreach ($pubIdPlugins as $pubIdPlugin) {
				$form->readUserVars($pubIdPlugin->getFormFieldNames());
				$clearFormFieldName = 'clear_' . $pubIdPlugin->getPubIdType();
				$form->readUserVars(array($clearFormFieldName));
			}
		}
	}

	/**
	 * Set the additional data from public identifier plugins.
	 * @param $form object CatalogEntryFormatMetadataForm
	 * @param $pubObject object A PublicationFormat object
	 */
	function execute(&$form, &$pubObject) {
		$pubIdPlugins =& PluginRegistry::loadCategory('pubIds', true);
		if (is_array($pubIdPlugins)) {
			foreach ($pubIdPlugins as $pubIdPlugin) {
				// Public ID data can only be changed as long
				// as no ID has been generated.
				$storedId = $pubObject->getStoredPubId($pubIdPlugin->getPubIdType());
				$fieldNames = $pubIdPlugin->getFormFieldNames();
				$excludeFormFieldName = $pubIdPlugin->getExcludeFormFieldName();
				$clearFormFieldName = 'clear_' . $pubIdPlugin->getPubIdType();
				foreach ($fieldNames as $fieldName) {
					$data = $form->getData($fieldName);
					// if the exclude checkbox is unselected
					if ($fieldName == $excludeFormFieldName && !isset($data))  {
						$data = 0;
					}
					$pubObject->setData($fieldName, $data);
					if ($data) {
						$this->_clearPubId($pubIdPlugin, $pubObject);
					} else if ($form->getData($clearFormFieldName)) {
						$this->_clearPubId($pubIdPlugin, $pubObject);
					}
				}
			}
		}
	}

	/**
	 * Clear a pubId from a pubObject.
	 * @param PubIdPlugin $pubIdPlugin
	 * @param Object $pubObject
	 */
	function _clearPubId($pubIdPlugin, $pubObject) {
		// clear the pubId:
		// delete the pubId from the DB
		$pubObjectType = $pubIdPlugin->getPubObjectType($pubObject);
		$dao = $pubIdPlugin->getDAO($pubObjectType);
		$dao->deletePubId($pubObject->getId(), $pubIdPlugin->getPubIdType());
		// set the object setting/data 'pub-id::...' to null, in order
		// not to be consideren in the DB object update later in the form
		$settingName = 'pub-id::'.$pubIdPlugin->getPubIdType();
		$pubObject->setData($settingName, null);
	}

}
?>
