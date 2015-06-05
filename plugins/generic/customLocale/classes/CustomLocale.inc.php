<?php

/**
 * @file StaticPage.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.staticPages
 * @class StaticPage
 * Data object representing a static page.
 */

class CustomLocale extends DataObject {
	/**
	 * Constructor
	 */
	function CustomLocale() {
		parent::DataObject();
	}

	//
	// Get/set methods
	//

	/**
	 * Get context ID
	 * @return string
	 */
	function getKey(){
		return $this->getData('key');
	}

	/**
	 * Set context ID
	 * @param $contextId int
	 */
	function setKey($key) {
		return $this->setData('key', $key);
	}


	function getLocale(){
		return $this->getData('locale');
	}

	/**
	 * Set context ID
	 * @param $contextId int
	 */
	function setLocale($locale) {
		return $this->setData('locale', $locale);
	}


	/**
	 * Get context ID
	 * @return string
	 */
	function getContextId(){
		return $this->getData('contextId');
	}

	/**
	 * Set context ID
	 * @param $contextId int
	 */
	function setContextId($contextId) {
		return $this->setData('contextId', $contextId);
	}


	/**
	 * Set page title
	 * @param string string
	 * @param locale
	 */
	function setFileTitle($title) {
		return $this->setData('filetitle', $title);
	}

	/**
	 * Get page title
	 * @param locale
	 * @return string
	 */
	function getFileTitle() {

		return $this->getData('filetitle');
	}



	/**
	 * Set page title
	 * @param string string
	 * @param locale
	 */
	function setTitle($title, $locale) {
		return $this->setData('title', $title, $locale);
	}

	/**
	 * Get page title
	 * @param locale
	 * @return string
	 */
	function getTitle($locale) {

		return $this->getData('title', $locale);
	}

	/**
	 * Get Localized page title
	 * @return string
	 */
	function getLocalizedTitle() {
		return $this->getLocalizedData('title');
	}


	/**
	 * Get page path string
	 * @return string
	 */
	function getPath() {
		return $this->getData('path');
	}

	 /**
	  * Set page path string
	  * @param $path string
	  */
	function setPath($path) {
		return $this->setData('path', $path);
	}

	/**
	 * Get page path string
	 * @return string
	 */
	function getFilePath() {
		return $this->getData('filepath');
	}

	 /**
	  * Set page path string
	  * @param $path string
	  */
	function setFilePath($filepath) {
		return $this->setData('filepath', $filepath);
	}


}

?>
