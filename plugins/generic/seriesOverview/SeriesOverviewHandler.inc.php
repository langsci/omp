<?php

/**
 * @file plugins/generic/seriesOverview/SeriesOverviewHandler.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesOverviewHandler
 *
 *
 */

import('classes.handler.Handler');
import('plugins.generic.seriesOverview.LangsciCommonDAO');
import('classes.monograph.PublishedMonographDAO');
import('classes.monograph.MonographDAO');
import('classes.press.SeriesDAO');
include('LangsciCommonFunctions.inc.php');

class SeriesOverviewHandler extends Handler {	

	function SeriesOverviewHandler() {
		parent::Handler();
	}

	function viewSeriesOverview($args, $request) {

		$langsciCommonDAO = new LangsciCommonDAO;

		$press = $request -> getPress();
		$seriesDao = DAORegistry::getDAO('SeriesDAO');
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');

		$seriesIterator = $seriesDao->getByPressId($press->getId());
		$series = array();
		$monographs = array();
		$mostRecentMonograph = array();
		while ($seriesObject = $seriesIterator->next()) {

			$series_id=$seriesObject->getId();
			$publishedMonographs = $publishedMonographDao->
					getBySeriesId($seriesObject->getId(), $press->getId())->toAssociativeArray();
			$numberOfBooks = sizeof($publishedMonographs);	
			$seriesGroup = 'incubation';
			if ($numberOfBooks>0) {
				$seriesGroup = 'series';
			}
			$series[$seriesGroup][$series_id]['seriesObject'] = $seriesObject;
			$series[$seriesGroup][$series_id]['title'] = $seriesObject->getLocalizedTitle();
			$series[$seriesGroup][$series_id]['numberOfBooks'] = $numberOfBooks;
			$series[$seriesGroup][$series_id]['link'] = $request->url($press,'catalog','series',$seriesObject->getPath());
			$monographsForSeries = array();
			$numberOfPublishedBooks = 0;
			foreach ($publishedMonographs as $monographKey => $publishedMonograph) {

				$localizedFullTitle = $publishedMonograph->getLocalizedFullTitle();

				if (strpos($localizedFullTitle,'Forthcoming:')===false) {
					$numberOfPublishedBooks++;
				}
				$monographsForSeries[$monographKey]['submissionId'] = $monographKey;
				$monographsForSeries[$monographKey]['publicationDate'] = $this->getPublicationDate($monographKey);
				$monographsForSeries[$monographKey]['fullTitle'] = $localizedFullTitle;
				$monographsForSeries[$monographKey]['title'] = $publishedMonograph->getLocalizedTitle();
				$monographsForSeries[$monographKey]['presentationString'] = getSubmissionPresentationString($monographKey);
				$monographsForSeries[$monographKey]['link'] = 
											$request->url($press,'catalog','book',$monographKey);
			}
			usort($monographsForSeries,'sort_books_by_title');
			$series[$seriesGroup][$series_id]['monographs'] = $monographsForSeries;
			$series[$seriesGroup][$series_id]['numberOfPublishedBooks'] = $numberOfPublishedBooks;
			$series[$seriesGroup][$series_id]['numberOfForthcomingBooks'] = $numberOfBooks-$numberOfPublishedBooks;
		}

		krsort($series);

		if (sizeof($series['incubation'])) {
			usort($series['incubation'],'sort_by_title_and_numberOfBooks');
		}
		if (sizeof($series['series'])) {
			usort($series['series'],'sort_by_title_and_numberOfBooks');
		}

		$mostRecentMonographs = array(); // key: series_id, value: id of most recent monograph
		foreach ($series['series'] as $seriesId => $singleSeries) { 
			$publicationDates = array();
			$seriesId = $singleSeries['seriesObject']->getId();
			foreach ($singleSeries['monographs'] as $key=>$monograph) {
				$publicationDates[$monograph['publicationDate']] = $monograph['submissionId'];
			}	
			krsort($publicationDates);
			$dates = array_keys($publicationDates);
			if ($dates[0]) {
 				$mostRecentMonographs[$seriesId] = $publicationDates[$dates[0]];
			} else {
 				$mostRecentMonographs[$seriesId] = null;
			}
		}

		$templateMgr = TemplateManager::getManager($request);

		$templateMgr->assign('mostRecentMonographs', $mostRecentMonographs);

		$templateMgr->assign('pageTitle', 'plugins.generic.title.seriesOverview');
		$templateMgr->assign('data', $data);
		$templateMgr->assign('baseUrl',$request->getBaseUrl());	
		$templateMgr->assign('imageDirectory',$imageDirectory);
		$templateMgr->assign('monographs',$monographs);
		$templateMgr->assign('series',$series);
		$templateMgr->assign('useImages',$press->getSetting('langsci_seriesOverview_useImages'));	

		$seriesOverviewPlugin = PluginRegistry::getPlugin('generic', SERIESOVERVIEW_PLUGIN_NAME);
		$templateMgr->display($seriesOverviewPlugin->getTemplatePath().'seriesOverview.tpl');
	}

	// get 'Publication date' for publication format 'Complete book'
	function getPublicationDate($submissionId) {

		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		if ($publishedMonograph) {

			$pubformats = $publishedMonograph->getPublicationFormats();
			for ($i=0; $i<sizeof($pubformats); $i++) {

				$formatName = implode($pubformats[$i]->getName());
				if ($formatName=="Complete book") {

					$pubdates = $pubformats[$i]->getPublicationDates();
					$pubdatesArray = $pubdates->toArray();
					for ($ii=0;$ii<sizeof($pubdatesArray);$ii++) {
						if ($pubdatesArray[$ii]->getRole()=="01") {
							return $pubdatesArray[$ii]->getDate();
						}
					}
				}
			}
		}
		return null;
	}

}

function sort_by_title_and_numberOfBooks($a, $b) {
	if ($b['numberOfBooks']!=$a['numberOfBooks']) {
		return  $b['numberOfBooks'] - $a['numberOfBooks'];
	} else {
    	return strcasecmp($a['title'],$b['title']);
	}
}

function sort_books_by_title($a, $b) {
	$aForthcoming = strpos($a['fullTitle'],'Forthcoming:');
	$bForthcoming = strpos($b['fullTitle'],'Forthcoming:');
	if ($aForthcoming===false) {
		if ($bForthcoming===false) {
			return strcasecmp($a['title'],$b['title']);
		} else {
			return -1;
		}
	}
	else {
		if ($bForthcoming===false) {
			return 1;
		} else {
			return strcasecmp($a['title'],$b['title']);
		}
	}
}


?>
