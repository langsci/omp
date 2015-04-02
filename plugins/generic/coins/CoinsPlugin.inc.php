<?php

/**
 * @file plugins/generic/coins/CoinsPlugin.inc.php
 *
 * Copyright (c) 2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CoinsPlugin
 * @ingroup plugins_generic_coins
 *
 * @brief COinS plugin class
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.monograph.PublishedMonographDAO');

class CoinsPlugin extends GenericPlugin {
	/**
	 * @see Plugin::register
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			HookRegistry::register ('TemplateManager::display', array($this, 'handleTemplateDisplay'));
		}
		return $success;
	}

	/**
	 * @see Plugin::getDisplayName
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.coins.displayName');
	}

	/**
	 * @see Plugin::getDescirption
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.coins.description');
	}

	/**
	 * @see Plugin::getInstallSitePluginSettingsFile
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Intercept the googlescholar template to add the COinS tag
	 * @param $hookName string Hook name
	 * @param $args array Array of hook parameters
	 * @return boolean false to continue processing subsequent hooks
	 */
	function handleTemplateInclude($hookName, $args) {
		$templateMgr =& $args[0];
		$smarty =& $args[1];
		if (!isset($smarty['smarty_include_tpl_file'])) return false;
		switch ($smarty['smarty_include_tpl_file']) {
			case 'catalog/book/googlescholar.tpl':
				
		/*		$publishedMonographDAO = new PublishedMonographDAO;
				$publishedMonographs = $publishedMonographDAO -> getByPressId(1);
				$publishedMonographsArray = $publishedMonographs ->toArray();
			
				$pubformats = $publishedMonographsArray[$i] ->getPublicationFormats();
				$test = $pubformats[0]->getName();

				echo "<br>name of publication format:" . implode($test);
				*/

				$request = $this->getRequest();
				//$templateMgr = TemplateManager::getManager($request);

				$publishedMonograph = $templateMgr->get_template_vars('publishedMonograph');
				//$journal = $templateMgr->get_template_vars('currentJournal');
				//$issue = $templateMgr->get_template_vars('issue');

				// get variables
				$authors = $publishedMonograph->getAuthors();
				$firstAuthor = $authors[0];
				$datePublished = $publishedMonograph->getDatePublished();
				$language = $publishedMonograph->getLocale();
				$publisher = "Language Science Press";
				$place = "Berlin";
				
				// get series title
				$seriesId = $publishedMonograph ->getSeriesId();
				$seriesDAO = new SeriesDAO;
				$series = $seriesDAO -> getById($seriesId,1);
				$seriesTitle = $series->getLocalizedFullTitle();
				
				$seriesPosition = $publishedMonograph ->getSeriesPosition();
				
				// put values in array 
				$vars = array(
					array('ctx_ver', 'Z39.88-2004'),
					array('rft_id', $request->url(null, 'catalog', 'book', $publishedMonograph->getId())),
					// coins id for book
					array('rft_val_fmt', 'info:ofi/fmt:kev:mtx:book'),
					// genre: book
					array('rft.genre', 'book'),
					// booktitle
					array('rft.btitle', $publishedMonograph->getLocalizedFullTitle()),
					// author
					array('rft.aulast', $firstAuthor->getLastName()),
					array('rft.aufirst', $firstAuthor->getFirstName()),
					array('rft.auinit', $firstAuthor->getMiddleName()),
				//	array('rft.au', $publishedMonograph->getAuthorString()),
				
					// series
					array('rft.series', $seriesTitle),
					
					// publisher
				//	array('rft.pub', $currentPress->getSetting('publisher')), 
					array('rft.publisher', $publisher), 
					array('rft.place', $place), 
					
					// published date
					array('rft.date', date('Y-m-d', strtotime($datePublished))),
					
					// language
					array('rft.language', substr($language, 0, 3)),
					
					
					//array('rft.title', $journal->getLocalizedName()),
					//array('rft.jtitle', $journal->getLocalizedName()),
					//array('rft.atitle', $article->getLocalizedTitle()),
					//array('rft.artnum', $article->getBestArticleId()),
					//array('rft.stitle', $journal->getLocalizedSetting('abbreviation')),
					//array('rft.volume', $issue->getVolume()),
					//array('rft.issue', $issue->getNumber()),
					
				);

			/*	$datePublished = $publishedMonograph->getDatePublished();
				if (!$datePublished) $datePublished = $issue->getDatePublished();
				if ($datePublished) {
					$vars[] = array('rft.date', date('Y-m-d', strtotime($datePublished)));
				} */

				foreach ($authors as $author) {
					$vars[] = array('rft.au', $author->getFullName());
				}

			//	if ($doi = $publishedMonograph->getPubId('doi')) $vars[] = array('rft_id', 'info:doi/' . $doi);
			//	if ($publishedMonograph->getPages()) $vars[] = array('rft.pages', $publishedMonograph->getPages());
				//if ($journal->getSetting('printIssn')) $vars[] = array('rft.issn', $journal->getSetting('printIssn'));
				//if ($journal->getSetting('onlineIssn')) $vars[] = array('rft.eissn', $journal->getSetting('onlineIssn'));

				$title = '';
				foreach ($vars as $entries) {
					list($name, $value) = $entries;
					$title .= $name . '=' . urlencode($value) . '&';
				}
				$title = htmlentities(substr($title, 0, -1));

				$templateMgr->assign('title', $title);
				$templateMgr->display($this->getTemplatePath() . 'coinsTag.tpl', 'text/html', 'CoinsPlugin::addCoinsTag');
				break;
		}
		return false;
	}

	/**
	 * Hook callback: Handle requests.
	 * @param $hookName string Hook name
	 * @param $args array Array of hook parameters
	 * @return boolean false to continue processing subsequent hooks
	 */
	function handleTemplateDisplay($hookName, $args) {
		$templateMgr =& $args[0];
		$template =& $args[1];
		switch ($template) {
			case 'catalog/book/book.tpl':
				HookRegistry::register ('TemplateManager::include', array($this, 'handleTemplateInclude'));
				break;
		}
		return false;
	}

}
?>
