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

class CustomLocaleForm extends Form {

	var $localeFiles;

	/** @var StaticPagesPlugin Static pages plugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $staticPagesPlugin StaticPagesPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function CustomLocaleForm($template) {
		parent::Form($template);
	}

	function setLocales($locales) {
		$this->locales = $locales;
	}

	function setLocaleFiles($localeFiles) {
		$this->localeFiles = $localeFiles;
	}

	function fetch($request, $template = null, $display = false) {

		// Set custom template.
		if (!is_null($template)) $this->_template = $template;

		// Call hooks based on the calling entity, assuming
		// this method is only called by a subclass. Results
		// in hook calls named e.g. "papergalleyform::display"
		// Note that class names are always lower case.
		$returner = null;
		if (HookRegistry::call(strtolower_codesafe(get_class($this)) . '::display', array($this, &$returner))) {
			return $returner;
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->setCacheability(CACHEABILITY_NO_STORE);


		// Attach this form object to the Form Builder Vocabulary for validation to work
		$fbv = $templateMgr->getFBV();
		$fbv->setForm($this);

		// custom locale specific data
		$templateMgr->assign('locales',$this->locales);
		$templateMgr->assign('localeFiles',$this->localeFiles);
		$templateMgr->assign('masterLocale', MASTER_LOCALE);

		$templateMgr->assign($this->_data);
		$templateMgr->assign('isError', !$this->isValid());
		$templateMgr->assign('errors', $this->getErrorsArray());

		$templateMgr->register_function('form_language_chooser', array($this, 'smartyFormLanguageChooser'));
		$templateMgr->assign('formLocales', $this->supportedLocales);

		// Determine the current locale to display fields with
		$templateMgr->assign('formLocale', $this->getFormLocale());

		// N.B: We have to call $templateMgr->display instead of ->fetch($display)
		// in order for the TemplateManager::display hook to be called
		$returner = $templateMgr->display($this->_template, null, null, $display);

		// Need to reset the FBV's form in case the template manager does another fetch on a template that is not within a form.
		$nullVar = null;
		$fbv->setForm($nullVar);

		return $returner;
	}

}

?>
