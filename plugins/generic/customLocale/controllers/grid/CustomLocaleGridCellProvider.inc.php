<?php

/**
 * @file StaticPageGridCellProvider.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2000-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageGridCellProvider
 * @ingroup controllers_grid_staticPages
 *
 * @brief Class for a cell provider to display information about static pages
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.linkAction.request.RedirectAction');

class CustomLocaleGridCellProvider extends GridCellProvider {
	


	/**
	 * Constructor
	 */
	function CustomLocaleGridCellProvider() {
		parent::GridCellProvider();
	}

	//
	// Template methods from GridCellProvider
	//
	/**
	 * Get cell actions associated with this row/column combination
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array an array of LinkAction instances
	 */
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
		$staticPage = $row->getData();
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		import('lib.pkp.classes.linkAction.request.AjaxAction');
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		switch ($column->getId()) {
			case 'path':
				$dispatcher = $request->getDispatcher();
				$router = $request->getRouter();

			/*	return array(new LinkAction(
					'edit',
					new RedirectAction(
							$dispatcher->url(
								$request, ROUTE_PAGE,
								null, 'management', 'settings', 'website',
								array('uid' => uniqid(),'neuerParame'=>"test"), // Force reload
								'customLocale' // Anchor for tab
							)
					),
					'edit',
					null,
					'tooltip'
				));*/


				return array(new LinkAction(
					'edit',
					new AjaxAction(	
						$router->url($request, null, null, 'edit', null, array('localeKey' => $staticPage->getPath()))
				//		$router->url($request, null, null, null, null, array('uid' => uniqid(),'localeKey' => $staticPage->getPath()))


					),					
					'edit',
					null
				));

			case 'filepath':
				$dispatcher = $request->getDispatcher();
				$router = $request->getRouter();

				return array(new LinkAction(
					'edit',
					new AjaxModal(
						$router->url($request, null, null, 'editLocaleFile', null, array('locale'=>$staticPage->getLocale(),'filePath' =>  $staticPage->getFilePath())),
						__('grid.action.edit'),
						'modal_edit',
						true
					),				
					'EDIT',
					null
				));

/*
				return array(new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						"dialogText",
						"title",
						$router->url($request, null, null, 'edit', null, array('staticPageId' => 111)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				));
*/
//	function RemoteActionConfirmationModal($dialogText, $title = null, $remoteAction = null, $titleIcon = null, $okButton = null, $cancelButton = null, $canClose = true)
/*
				return array(new LinkAction(
					'edit1',
					new RedirectAction(
							$dispatcher->url(
								$request, ROUTE_PAGE,
								null, 'management', 'settings', 'website',
								array('uid' => uniqid(),'key'=>"test"), // Force reload
								'customLocale' // Anchor for tab
							)
					),
					'edit',
					null,
					'tooltip'
				));*/

// function LinkAction($id, &$actionRequest, $title = null, $image = null, $toolTip = null)
//function RedirectAction($url, $name = '_self', $specs = '') 
//	function url($request, $shortcut, $newContext = null, $handler = null, $op = null, $path = null,
//				$params = null, $anchor = null, $escape = false)


/*
			case 'pathx':
				$dispatcher = $request->getDispatcher();
				return array(new LinkAction(

					'details',

					new RedirectAction(
						$dispatcher->url($request, ROUTE_PAGE, null) . '/' . $staticPage->getPath(),
						'_blank'
					),


					$staticPage->getPath()
					)
				);*/
			default:
				return parent::getCellActions($request, $row, $column, $position);
		}
	}

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$staticPage = $row->getData();

		switch ($column->getId()) {
			case 'path':
				// The action has the label
				return array('label' => '');
			case 'filepath':
				// The action has the label
				return array('label' => '');
			case 'title':
				return array('label' => $staticPage->getLocalizedTitle());
			case 'filetitle':
				return array('label' => $staticPage->getFileTitle());
			case 'key':
				return array('label' => $staticPage->getKey());
		}
	}
}

?>
