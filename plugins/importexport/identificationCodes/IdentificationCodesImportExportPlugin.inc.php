<?php

/**
 * @file plugins/importexport/native/NativeImportExportPlugin.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NativeImportExportPlugin
 * @ingroup plugins_importexport_native
 *
 * @brief Native XML import/export plugin
 */

import('classes.plugins.ImportExportPlugin');
import('plugins.importexport.identificationCodes.IdentificationCodesDAO');
import('classes.monograph.MonographDAO');

class IdentificationCodesImportExportPlugin extends ImportExportPlugin {
	/**
	 * Constructor
	 */
	function IdentificationCodesImportExportPlugin() {
		parent::ImportExportPlugin();
	}

	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @param $path string
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		$this->addLocaleData();
		return $success;
	}

	/**
	 * @see Plugin::getTemplatePath($inCore)
	 */
	function getTemplatePath($inCore = false) {
		return parent::getTemplatePath($inCore) . 'templates/';
	}

	/**
	 * Get the name of this plugin. The name must be unique within
	 * its category.
	 * @return String name of plugin
	 */
	function getName() {
		return 'IdentificationCodesImportExportPlugin';
	}

	/**
	 * Get the display name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.importexport.identificationCodes.displayName');
	}

	/**
	 * Get the display description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.importexport.identificationCodes.description');
	}


	/**
	 * @copydoc PKPPlugin::getManagementVerbs()
	 */
	function getManagementVerbs() {

		$verbs = parent::getManagementVerbs();
		$verbs[] = array('settings', __('plugins.importexport.identificationCodes.settings'));
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

		switch($verbName) {
			case 'settings':

				import('lib.pkp.classes.linkAction.request.AjaxLegacyPluginModal');
				$actionRequest = new AjaxLegacyPluginModal(
					$router->url($request, null, null, 'plugin', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'importexport')),
					$this->getDisplayName()
				);
				return new LinkAction($verbName, $actionRequest, $verbLocalized, null);

			case 'importexport':
				return parent::getManagementVerbLinkAction($request, $verb);

			default:
				return array();
		}
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

					$this->import('IdentificationCodesSettingsForm');
					$form = new IdentificationCodesSettingsForm($this, $press);

					if ($request->getUserVar('save')) {

						$form->readInputData();
						if ($form->validate()) {
							$form->execute();
							$message = NOTIFICATION_TYPE_SUCCESS;
							$messageParams = array('contents' => __('plugins.importexport.identificationCodes.form.saved'));
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
	 * Display the plugin.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function display($args, $request) {
		$templateMgr = TemplateManager::getManager($request);
		$press = $request->getPress();
		$context = $request->getContext();		
		$contextId = $context->getId();

		parent::display($args, $request);
		$templateMgr->assign('plugin', $this);

		switch (array_shift($args)) {
			case 'index':
			case '':

				$authorizedUserGroups = array(ROLE_ID_SITE_ADMIN,ROLE_ID_MANAGER);
				$userRoles = array(ROLE_ID_MANAGER);//$this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);

				// redirect to index page if user does not have the rights
				$user = $request->getUser();
				if (!array_intersect($authorizedUserGroups, $userRoles)) {
					$request->redirect(null, 'index');
				}

				// get setting data
				$identifcationCodesSettings = array_map('trim', explode(',', $press->getSetting('langsci_identificationCodes_codes')));
				$displaySettings = $press->getSetting('langsci_identificationCodes_display');

				// get codes from the ONIX code list
				$onixCodelistItemDao = DAORegistry::getDAO('ONIXCodelistItemDAO');
				$onixCodes = $onixCodelistItemDao->getCodes('List5');

				// get all codes from the settings that really exist
				$selectedIdentificationCodes = array();
				foreach ($onixCodes as $id => $codename) {
					if (in_array(trim($codename),$identifcationCodesSettings,true)) {
						$selectedIdentificationCodes[] = $id;
					}
				}

				// get all code values from the database
				$identificationCodesDAO = new IdentificationCodesDAO();
				$identificationCodes = $identificationCodesDAO->getData($press->getPrimaryLocale());

				// get full title
				$monographDao = new MonographDAO;

				foreach($identificationCodes as $publicationFormatId => $item) {
					$monograph = $monographDao->getById($item['subId'],$contextId);
					if ($monograph) {
						$identificationCodes[$publicationFormatId]['title'] = $monograph->getLocalizedPrefix()." ".$monograph->getLocalizedTitle();
					}
				}

				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->assign('pageTitle', 'plugins.importexport.title.identificationCodes');
				$templateMgr->assign('identificationCodes', $identificationCodes);
				$templateMgr->assign('selectedIdentificationCodes', $selectedIdentificationCodes);
				$templateMgr->assign('onixCodes', $onixCodes);
				$templateMgr->assign('displaySettings', $displaySettings);
				$templateMgr->display($this->getTemplatePath() . 'index.tpl');
				break;

			case 'uploadImportXML':

				$user = $request->getUser();
				import('lib.pkp.classes.file.TemporaryFileManager');
				$temporaryFileManager = new TemporaryFileManager();
				$temporaryFile = $temporaryFileManager->handleUpload('uploadedFile', $user->getId());
				if ($temporaryFile) {
					$json = new JSONMessage(true);
					$json->setAdditionalAttributes(array(
						'temporaryFileId' => $temporaryFile->getId()
					));
				} else {
					$json = new JSONMessage(false, __('common.uploadFailed'));
				}

				return $json->getString();

			case 'importBounce':

				$import=false;
				if (isset($_POST['submitFormButton2'])) {
					$import=true;
				}

				$json = new JSONMessage(true);
				$json->setEvent('addTab', array(
					'title' => __('plugins.importexport.identificationCodes.results'),
					'url' => $request->url(null, null, null, array('plugin', $this->getName(), 'import'),
								array('temporaryFileId' => $request->getUserVar('temporaryFileId'),'import'=>$import)),
				));
				return $json->getString();

			case 'import':

				$import = $request->getUserVar('import');

				// get data from file
				$temporaryFileId = $request->getUserVar('temporaryFileId');
				$temporaryFileDao = DAORegistry::getDAO('TemporaryFileDAO');
				$user = $request->getUser();
				$temporaryFile = $temporaryFileDao->getTemporaryFile($temporaryFileId, $user->getId());
				if (!$temporaryFile) {
					$json = new JSONMessage(true, __('plugins.inportexport.identificationCodes.uploadFile'));
					return $json->getString();
				}
				$temporaryFilePath = $temporaryFile->getFilePath();
				$data_strings = str_getcsv(file_get_contents($temporaryFilePath),"\n");

				// check data (header, number of rows, number of items in each row)
				$formatOkay = true;

				// check header
				$header = str_getcsv($data_strings[0],",");
				if ($header[0]!=='SubId' ||
					 $header[1]!=='Author' ||
					 $header[2]!=='Title' ||
					 $header[3]!=='PubFormat') {
					$formatOkay = false;
				}

				// check number of rows
				$numberOfRows = sizeof($data_strings);
				if ($numberOfRows<2) {
					$formatOkay = false;
				}

				// echek number of items in each row
				$numberOfColumns = sizeof($header);
				for ($i=0;$i<$numberOfRows;$i++) {
					$data[$i] = str_getcsv($data_strings[$i],",");
					if (sizeof($data[$i])!==$numberOfColumns) {
						$formatOkay = false;
					}
				}

				// error message if format is not okay
				if (!$formatOkay) {
					$templateMgr->assign('errorMessage','Incorrect data format. Please import comma separated csv-file.');
					$json = new JSONMessage(true, $templateMgr->fetch($this->getTemplatePath() . 'results.tpl'));
					return $json->getString();
				} 

				$onixCodelistItemDao = DAORegistry::getDAO('ONIXCodelistItemDAO');
				$onixCodes = $onixCodelistItemDao->getCodes('List5');
				$insertedCodes = array();
				$nonInsertedCodes = array();
				$countInsertedItems = 0;
				$countNonInsertedItems = 0;
				for ($i=0;$i<sizeof($data);$i++) {            // for all rows in the imported file
					for ($ii=3;$ii<sizeof($data[0]);$ii++) {  // for all codes in the imported file

						// get codes from plugin settings and check if there are codes of that name in the onix list)
						// submission id must be all digit
						$codeId = array_search($data[0][$ii],$onixCodes);
						if ($codeId && $data[$i][$ii]!=='' && ctype_digit($data[$i][0])) {

							$submissionId = $data[$i][0];
							$publicationFormat = $data[$i][3];
							$codeValue = $data[$i][$ii];
					
							$title = "unknown";	
							$monographDAO = new MonographDAO;
 							$monograph = $monographDAO -> getById($submissionId,$contextId);
							if ($monograph) {
								$title = $monograph->getLocalizedPrefix()." ".$monograph->getLocalizedTitle();
							}

							// get publication format ids for the name of the publication format specified in the settings
							$identificationCodesDao = new IdentificationCodesDAO();

							$submissionExists=$identificationCodesDao->submissionExists($submissionId);

							$publicationFormatIds = $identificationCodesDao->
													getPublicationFormatIds($submissionId,$publicationFormat);
							// if publication format or submission cannot be found: no entry
							if (!$submissionExists) {
								$this->insertCodeInformation($nonInsertedCodes, $countNonInsertedItems++,
																		array('submission not found',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title)); 
							}
							elseif (sizeof($publicationFormatIds)==0) {
								$this->insertCodeInformation($nonInsertedCodes, $countNonInsertedItems++,
																		array('publication format not found',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title)); 
							}
							// if there are 2 or more ids: no entry can be made (ambiguity)
							elseif (sizeof($publicationFormatIds)>1) {
								$this->insertCodeInformation($nonInsertedCodes, $countNonInsertedItems++,
																		array('publication format not unique',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title)); 
							}
							elseif (sizeof($publicationFormatIds)==1) {
			
								// get code value of the publicationFormat/CodeId specified in the settings
								$givenCodeValue = $identificationCodesDao->getCodeValue($publicationFormatIds[0],$codeId);

								if (!$givenCodeValue) {
									// if there is no code value yet: create new entry
									if ($import) {
										$identificationCodesDao->insertCode($publicationFormatIds[0],$codeId, $codeValue);
									}
									$this->insertCodeInformation($insertedCodes, $countInsertedItems++,
																		array('new entry',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title)); 
								} else {
									// if there is a code value in the database
									if (strcmp($codeValue,$givenCodeValue)==0) {
										// if it is identicial to the code value in the settings: no change
										$this->insertCodeInformation($nonInsertedCodes, $countNonInsertedItems++,
																		array('entry already exists',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title));
									} else {
										// if there is a different code value: update
										if ($import) {
											$identificationCodesDao->updateCodeValue($publicationFormatIds[0],$codeId, $codeValue);
										}
										$this->insertCodeInformation($insertedCodes, $countInsertedItems++,
																		array('entry updated',$submissionId,$publicationFormat,$onixCodes[$codeId],$codeValue,$title));
									}
								}
							} 
						}
					}
				}				

				// prepare and load template
				$templateMgr->assign('insertedCodes',$insertedCodes);
				$templateMgr->assign('nonInsertedCodes',$nonInsertedCodes);
				$templateMgr->assign('import',$import);
				$json = new JSONMessage(true, $templateMgr->fetch($this->getTemplatePath() . 'results.tpl'));
				return $json->getString();

			case 'export':

				// get codes from the settings
				$identifcationCodesSettings = array_map('trim', explode(',', $press->getSetting('langsci_identificationCodes_codes')));

				// get codes from the ONIX code list
				$onixCodelistItemDao = DAORegistry::getDAO('ONIXCodelistItemDAO');
				$onixCodes = $onixCodelistItemDao->getCodes('List5');

				// get all codes from the settings that really exist
				$selectedIdentificationCodes = array();
				foreach ($onixCodes as $id => $codename) {
					if (in_array(trim($codename),$identifcationCodesSettings,true)) {
						$selectedIdentificationCodes[] = $id;
					}
				}

				// get all code values from the database
				$identificationCodesDAO = new IdentificationCodesDAO();
				$identificationCodes = $identificationCodesDAO->getData($press->getPrimaryLocale());

				// create header
				$data = array();
				$data[0]['submissionId'] = 'SubId';
				$data[0]['author'] = 'Author';
				$data[0]['title'] = 'Title';
				$data[0]['publicationFormat'] = 'PubFormat';
				foreach ($selectedIdentificationCodes as $code) {
					$data[0][$onixCodes[$code]] = $onixCodes[$code];
				}
				// insert identification codes
				$count = 1;
				$monographDao = new MonographDAO;
				foreach ($identificationCodes as $identificationCode) {

					$submissionId = $identificationCode['subId'];
					$data[$count]['subId'] = $identificationCode['subId'];
					$monograph = $monographDao->getById($submissionId,$contextId);
					if ($monograph) {
						$data[$count]['author'] = $monograph->getFirstAuthor();
						$data[$count]['title'] = $monograph->getLocalizedPrefix()." ".$monograph->getLocalizedTitle();
					}

					$data[$count]['publicationFormat'] = $identificationCode['publicationFormat'];
					foreach ($selectedIdentificationCodes as $code) {
						$data[$count][$onixCodes[$code]] =$identificationCode[$code];
					}
					$count++;
				}
				// output data
				header("Content-Type: text/csv; charset=utf-8");
				header("Content-Disposition: attachment; filename=identificationCodes.csv");
				$output = fopen("php://output", "w");
				foreach ($data as $row) {
				  fputcsv($output, $row); // here you can change delimiter/enclosure
				}
				fclose($output);
				break;

			default:
				$dispatcher = $request->getDispatcher();
				$dispatcher->handle404();
		}
	}

	private function insertCodeInformation(&$codes, $count, $values) {

		$codes[$count]['note'] = $values[0];
		$codes[$count]['subId'] = $values[1];
		$codes[$count]['publicationFormat'] = $values[2];
		$codes[$count]['codeName'] = $values[3];
		$codes[$count]['codeValue'] = $values[4];
		$codes[$count]['title'] = $values[5];
	}

							

}

?>
