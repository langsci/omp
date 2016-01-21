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

class SeriesOverviewHandler extends Handler {	

	function SeriesOverviewHandler() {
		parent::Handler();
	}

	function viewSeriesOverview($args, $request) {

		$press = $request -> getPress();
		$seriesDao = DAORegistry::getDAO('SeriesDAO');
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');

		$seriesIterator = $seriesDao->getByPressId($press->getId());
		$series = array();
		$monographs = array();
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
			$series[$seriesGroup][$series_id]['numberOfBooks'] = $numberOfBooks;
			$series[$seriesGroup][$series_id]['link'] = $request->url($press,'catalog','series',$seriesObject->getPath());

			$monographsForSeries = array();
			$numberOfPublishedBooks = 0;
			foreach ($publishedMonographs as $monographKey => $value) {
				$localizedFullTitle = $value->getLocalizedFullTitle();

				if (strpos($localizedFullTitle,'Forthcoming:')===false) {
					$numberOfPublishedBooks++;
				}

				$monographsForSeries[$monographKey]['title'] = $value->getLocalizedTitle();
				$monographsForSeries[$monographKey]['fullTitle'] = $localizedFullTitle;
				$monographsForSeries[$monographKey]['link'] = 
											$request->url($press,'catalog','book',$monographKey);
			}
			usort($monographsForSeries,'sort_books_by_title');
			$series[$seriesGroup][$series_id]['monographs'] = $monographsForSeries;
			$series[$seriesGroup][$series_id]['numberOfPublishedBooks'] = $numberOfPublishedBooks;
			$series[$seriesGroup][$series_id]['numberOfForthcomingBooks'] = $numberOfBooks-$numberOfPublishedBooks;
		}
		krsort($series);
		usort($series['incubation'],'sort_by_title_and_numberOfBooks');
		usort($series['series'],'sort_by_title_and_numberOfBooks');

		$templateMgr = TemplateManager::getManager($request);
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
