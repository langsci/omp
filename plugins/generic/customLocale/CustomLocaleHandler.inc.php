<?php

/**
 * @file hallOfFameHandler.inc.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameHandler
 */

import('classes.handler.Handler');

class CustomLocaleHandler extends Handler {

	/**
	 * Constructor
	 */
	function CustomLocaleHandler() {
		parent::Handler();
	}

	/**
	 * Handle view page request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function printCustomLocaleChanges($args, $request) {

		$press = Request::getPress();
		$pressId = $press->getId();

		$publicFilesDir = Config::getVar('files', 'public_files_dir');
		$customLocaleDir = $publicFilesDir . DIRECTORY_SEPARATOR . 'presses' . DIRECTORY_SEPARATOR . $pressId . DIRECTORY_SEPARATOR . CUSTOM_LOCALE_DIR;

		$absolutePath = dirname(__FILE__); 
		$ompPath = str_replace('/plugins/generic/customLocale','',$absolutePath);
		$customLocalePath = $ompPath.DIRECTORY_SEPARATOR .$customLocaleDir;

		// get all xml-files in the custom locale directory 		
		$directory = new RecursiveDirectoryIterator($customLocalePath);
		$iterator = new RecursiveIteratorIterator($directory);
		$regex = new RegexIterator($iterator, '/^.+\.xml$/i', RecursiveRegexIterator::GET_MATCH);
		$files = iterator_to_array($regex);
		$fileKeys = array_keys($files);

		import('lib.pkp.classes.file.FileManager');
		import('lib.pkp.classes.file.EditableLocaleFile');
		
		$output = "";

		// iterate through all customized files
		for ($i=0; $i<sizeof($fileKeys);$i++) {

			$pathToFile = $fileKeys[$i];
			$posLib = strpos($pathToFile,'lib');
			$posLocale = strpos($pathToFile,'locale');
			$posPlugins = strpos($pathToFile,'plugins');

			$ompFile = '';
			if (!$posLib===false) {
				$ompFile = substr($pathToFile,$posLib);
			} else if (!$posPlugins===false) {
				$ompFile = substr($pathToFile,$posPlugins);
			}
			else {
				$ompFile = substr($pathToFile,$posLocale);
			}
	
			$fileManagerCustomized = new FileManager();
			$localeContentsCustomized = null;
			if ($fileManagerCustomized->fileExists($fileKeys[$i])) {
				$localeContentsCustomized = EditableLocaleFile::load($fileKeys[$i]);
			}

			$fileManager = new FileManager();
			$localeContents = null;
			if ($fileManager->fileExists($ompFile)) {
				$localeContents = EditableLocaleFile::load($ompFile);
			}

			$localeKeys = array_keys($localeContentsCustomized);

			if (sizeof($localeKeys)>0) {
				$output = $output . "\nFile: " . $ompFile;
			}

			for ($ii=0; $ii<sizeof($localeKeys);$ii++) {
				$pos = $ii+1;
				$output = $output . "\n\n" . $pos .". locale key: " . $localeKeys[$ii];
				$output = $output . "\n\n	original content:   " . $localeContents[$localeKeys[$ii]];
				$output = $output . "\n	customized content: " . $localeContentsCustomized[$localeKeys[$ii]];
			}
			if (sizeof($localeKeys)>0) {
				$output = $output . "\n\n__________________________________________________________________________________\n\n";
			}
			
		}

		$filename = 'customLocale_changes.txt';
		header("Content-Type: text/plain");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Length: " . strlen($output));
		echo $output;
	}


}

?>
