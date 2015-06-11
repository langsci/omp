<?php
ini_set('memory_limit', '1024M');
/**
 * @file controllers/grid/StaticPageGridHandler.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageGridHandler
 * @ingroup controllers_grid_staticPages
 *
 * @brief Handle static pages grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.customLocale.controllers.grid.CustomLocaleGridRow');
import('plugins.generic.customLocale.controllers.grid.CustomLocaleGridCellProvider');
import('classes.handler.Handler');
import('plugins.generic.customLocale.classes.CustomLocale');

require_once('CustomLocaleAction.inc.php');

class CustomLocaleGridHandler extends GridHandler {

	 var $form;

	/** @var StaticPagesPlugin The static pages plugin */
	static $plugin;

	/**
	 * Set the static pages plugin.
	 * @param $plugin StaticPagesPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Constructor
	 */
	function CustomLocaleGridHandler() {

		parent::GridHandler();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('index', 'edit', 'editLocaleFile', 'updateLocale', 'fetchGrid', 'fetchRow','searchForLocale')
		);
	}

	function correctCr($value) {
		return str_replace("\r\n", "\n", $value);
	}

	function updateLocale($args,$request) {

		$press =& Request::getPress();
		$pressId = $press->getId();
		$locale = $args['locale'];
		$filename = $args['key'];
		$currentPage = $args['currentPage'];
		$searchKey = $args['searchKey'];
		$searchString = $args['searchString'];

		// don't save changes if the locale is searched
		if (!$searchKey) {

			// save changes

			$changes = $args['changes'];

			$customFilesDir = Config::getVar('files', 'public_files_dir') .
									DIRECTORY_SEPARATOR . 'presses' . DIRECTORY_SEPARATOR . $pressId . DIRECTORY_SEPARATOR . CUSTOM_LOCALE_DIR . DIRECTORY_SEPARATOR . $locale;
			$customFilePath = $customFilesDir . DIRECTORY_SEPARATOR . $filename;

			// Create empty custom locale file if it doesn't exist
			import('lib.pkp.classes.file.FileManager');
			$fileManager = new FileManager();

			import('lib.pkp.classes.file.EditableLocaleFile');
			if (!$fileManager->fileExists($customFilePath)) {

				$numParentDirs = substr_count($customFilePath, DIRECTORY_SEPARATOR); 
				$parentDirs = '';
				for ($i=0; $i<$numParentDirs; $i++) {
					$parentDirs .= '..' . DIRECTORY_SEPARATOR;
				}

				$newFileContents = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$newFileContents .= '<!DOCTYPE locale SYSTEM "' . $parentDirs . 'lib' . DIRECTORY_SEPARATOR . 'pkp' . DIRECTORY_SEPARATOR . 'dtd' . DIRECTORY_SEPARATOR . 'locale.dtd' . '">' . "\n";
				$newFileContents .= '<locale name="' . $locale . '">' . "\n";
				$newFileContents .= '</locale>';
				$fileManager->writeFile($customFilePath, $newFileContents);
			}

			if ($args['nextPage']) {
				$currentPage = $args['nextPage'];
			}

			$file = new EditableLocaleFile($locale, $customFilePath);

			while (!empty($changes)) {
				$key = array_shift($changes);
				$value = $this->correctCr(array_shift($changes));
				if (!empty($value)) {
					if (!$file->update($key, $value)) {
						$file->insert($key, $value);
					}
				} else {
					$file->delete($key);
				}
			}
			$file->write();
		}

		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.customLocale.controllers.grid.form.LocaleFileForm');

		$customLocalePlugin = self::$plugin;
		$localeFileForm = new LocaleFileForm(self::$plugin, $context->getId(), $filename, $locale);

		$localeFileForm->initData();
		$json = new JSONMessage(true, $localeFileForm->fetch($request,$currentPage,$searchKey,$searchString));

		return $json->getString();


	}

	// Overridden template methods
	//
	/**
	 * @copydoc Gridhandler::initialize()
	 */
	function initialize($request, $args = null) {

		parent::initialize($request);

		$context = $request->getContext();
		$press = $request -> getPress();

		// Set the grid details.
		$this->setTitle('plugins.generic.customLocale.customLocaleFiles');
		$this->setInstructions('plugins.generic.customLocale.introductionFiles');
		$this->setEmptyRowText('plugins.generic.customLocale.noneCreated');
		
		// Columns
		$cellProvider = new CustomLocaleGridCellProvider();

		$this->addColumn(new GridColumn(
			'filetitle',
			'plugins.generic.customLocale.files.pageTitle',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));

		$this->addColumn(new GridColumn(
			'filepath',
			'plugins.generic.customLocale.path',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));

	}

	function loadData($request, $filter) {

		$press = $request -> getPress();
		$locales = $press->getSupportedLocaleNames();

		$localeKeys = array_keys($locales);
		$locale = $localeKeys[$filter['locale']];
		$search = $filter['search'];

		if ($locale==null) {  // todo: bessere Lösung?
			$locale="en_US";
		}


		$localeFiles = CustomLocaleAction::getLocaleFiles($locale);

		$localeFilesSelected = array();
		$count = 0;
		if ($search!=='') {
			for ($i=0; $i<sizeof($localeFiles); $i++) {
				if (strpos(strtolower($localeFiles[$i]),strtolower($search)) !== false) {
					$localeFilesSelected[$count] = $localeFiles[$i];
					$count++;
				}
			}
		}
		else {
			$localeFilesSelected = $localeFiles;
		}

		$gridDataElements = array();
		for ($i=0; $i<sizeof($localeFilesSelected); $i++) {
			$customLocale = new CustomLocale();
			$customLocale->setId($i);
			$customLocale->setLocale($locale);
			$customLocale->setFilePath($localeFilesSelected[$i]);
			$customLocale->setContextId($request->getContext()->getId());   
			$customLocale->setFileTitle($localeFilesSelected[$i]);
			$gridDataElements[]=$customLocale;
		}

		return $gridDataElements;
	}

	/**
	 * @copydoc GridHandler::initFeatures()
	 */
	function initFeatures($request, $args) {
		import('lib.pkp.classes.controllers.grid.feature.PagingFeature');
		return array(new PagingFeature());
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @see Gridhandler::getPublishChangeEvents()
	 * @return array List of events that should be published upon change
	 * Used to update the site context switcher upon create/delete.
	 */
	function getPublishChangeEvents() {
		return array('updateSidebar');
	}

	/**
	 * @copydoc Gridhandler::getRowInstance()
	 */
	function getRowInstance() {
		return new CustomLocaleGridRow();
	}

	//
	// Public Grid Actions
	//
	/**
	 * Display the grid's containing page.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function index($args, $request) {

		$press = $request -> getPress();

		import('plugins.generic.customLocale.controllers.grid.form.CustomLocaleForm');

		$form = new CustomLocaleForm(self::$plugin->getTemplatePath() . 'customLocale.tpl');
		$templateMgr = TemplateManager::getManager($request);
		$json = new JSONMessage(true, $form->fetch($request, null, false));

		return $json->getString();
	}


	/**
	 * @copydoc GridHandler::getFilterForm()
	 * @return string Filter template.
	 */
	function getFilterForm() {

		$customLocalePlugin = self::$plugin;
		$templatePath = $customLocalePlugin->getTemplatePath();
		return $templatePath . 'customLocaleGridFilter.tpl';
	}

	function renderFilter($request) {
		$context = $request->getContext();
		$press = $request -> getPress();
		$locales = $press->getSupportedLocaleNames();

		$localeOptions = array();
		$keys = array_keys($locales);
		for ($i=0; $i<sizeof($locales); $i++) {
			$localeOptions[$i] = $keys[$i];
		}

		$fieldOptions = array(
			CUSTOMLOCALE_FIELD_PATH => 'fieldopt1',
		);

		$matchOptions = array(
			'contains' => 'form.contains',
			'is' => 'form.is'
		);

		$filterData = array(
			'localeOptions' => $localeOptions,
			'fieldOptions' => $fieldOptions,
			'matchOptions' => $matchOptions
		);

		return parent::renderFilter($request, $filterData);
	}

	/**
	 * @copydoc GridHandler::getFilterSelectionData()
	 * @return array Filter selection data.
	 */
	function getFilterSelectionData($request) {
		// Get the search terms.
		$locale = $request->getUserVar('locale') ? (int)$request->getUserVar('locale') : null;
		$searchField = $request->getUserVar('searchField');
		$searchMatch = $request->getUserVar('searchMatch');
		$search = $request->getUserVar('search');

		return $filterSelectionData = array(
			'locale' => $locale,
			'searchField' => $searchField,
			'searchMatch' => $searchMatch,
			'search' => $search ? $search : ''
		);
	}

	function editLocaleFile($args, $request) {

		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.customLocale.controllers.grid.form.LocaleFileForm');

		$customLocalePlugin = self::$plugin;
		$localeFileForm = new LocaleFileForm(self::$plugin, $context->getId(), $args['filePath'], $args['locale']);

		$localeFileForm->initData();
		$json = new JSONMessage(true, $localeFileForm->fetch($request));

/*
$file = fopen("test.txt","a");
fwrite($file,"\nCLGH->editLcaoleFile, filePath:  " . $args['filePath'] . " locale: " .  $args['locale']);
fclose($file);*/

		return $json->getString();
	}
}

?>
