<?php

/**
 * @file plugins/generic/seriesPage/SeriesPageHandler.inc.php
 *
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesPageHandler
 *
 *
 */

import('classes.handler.Handler');
import('plugins.generic.seriesPage.SeriesPageDAO');


class SeriesPageHandler extends Handler {	

	function series($args, $request) {

		$seriesPageDAO = new SeriesPageDAO;
		$series = $seriesPageDAO->getSeries();
		$submissionIds = $seriesPageDAO->getSubmissionIds();

		$baseUrl = $request->getBaseUrl();
		$pressPath = $this->getPressPath($request);



		$data = array();
		for ($i=0;$i<sizeof($series);$i++) {
			$data[$i]['link'] = "<a href=".$pressPath."/catalog/series/".$series[$i][0].">".$series[$i][1]."</a>";
			$data[$i]['image'] = "<img src='" .$baseUrl."/plugins/generic/seriesPage/img/".$series[$i][0].".png' alt='".$series[$i][0]."'>";
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.seriesPage');
		$templateMgr->assign('data', $data);
		$templateMgr->assign('baseUrl',$baseUrl);	

		$seriesPagePlugin = PluginRegistry::getPlugin('generic', SERIESPAGE_PLUGIN_NAME);
		$templateMgr->display($seriesPagePlugin->getTemplatePath().'seriesPage.tpl');

	}

	function getPressPath(&$request) {
		$press = $request -> getPress();
		$pressPath = $press -> getPath();
 		$completeUrl = $request->getCompleteUrl();
		return substr($completeUrl,0,strpos($completeUrl,$pressPath)) . $pressPath ;
	}

}

?>
