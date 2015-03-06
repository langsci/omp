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

 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI],"series")-1);
		$pressUrl = $host ."/".$shortUrl;

		$data = array();
		for ($i=0;$i<sizeof($series);$i++) {
			$data[$i]['link'] = "<a href=".$pressUrl."catalog/series/".$series[$i][0].">".$series[$i][1]."</a>";
			$data[$i]['image'] = "<img src='" .$this->getOmpUrl('series')."plugins/generic/seriesPage/img/".$series[$i][0].".png' alt='".$series[$i][0]."'>";
		}

		$templateMgr = $this->getTemplateManager($request);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.seriesPage');
		$templateMgr->assign('data', $data);
		$this->displayTemplate($templateMgr,'page.tpl');
	}


	function getOmpUrl($cut) {
 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI], $cut)-1);
		$nativeUrl = substr($shortUrl ,0,strpos($shortUrl, '/'));
		if (!$nativeUrl=="") {
			$nativeUrl = $nativeUrl . "/";
		}
		return $host ."/".$nativeUrl;	
	}

	function getTemplateManager($request)	{
		$this->validate();
		$press = $request->getPress();
		$this->setupTemplate($request, $press);
		$contentOnly = $request->getUserVar('contentOnly');
		$templateMgr = TemplateManager::getManager($request);
		$server_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";       
		$templateMgr->assign('ompUrl',substr($server_path,0,strpos($server_path, 'index.php')));	
		return $templateMgr;
	}

	function displayTemplate($templateMgr,$filename) {
		$seriesPagePlugin = PluginRegistry::getPlugin('generic', SERIESPAGE_PLUGIN_NAME);
		$templateMgr->display($seriesPagePlugin->getTemplatePath().$filename);
	}

}

?>
