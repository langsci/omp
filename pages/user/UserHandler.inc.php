<?php

/**
 * @file pages/user/UserHandler.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserHandler
 * @ingroup pages_user
 *
 * @brief Handle requests for user functions.
 */

import('lib.pkp.pages.user.PKPUserHandler');

class UserHandler extends PKPUserHandler {
	/**
	 * Constructor
	 */
	function UserHandler() {
		parent::PKPUserHandler();
	}

	/**
	 * @see PKPHandler::initialize()
	 */
	function initialize($request, &$args) {
		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_GRID);
		parent::initialize($request, $args);
	}

	/**
	 * Index page; redirect to profile
	 */
	function index($args, $request) {
		$request->redirect(null, null, 'profile');
	}

	/**
	 * Become a given role.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function become($args, $request) {
		parent::validate(true);

		$press = $request->getPress();
		$user = $request->getUser();

		switch (array_shift($args)) {
			case 'author':
				$roleId = ROLE_ID_AUTHOR;
				$setting = 'allowRegAuthor';
				$deniedKey = 'user.noRoles.submitMonographRegClosed';
				break;
			case 'reviewer':
				$roleId = ROLE_ID_REVIEWER;
				$setting = 'allowRegReviewer';
				$deniedKey = 'user.noRoles.regReviewerClosed';
				break;
			default:
				$request->redirect(null, null, 'index');
		}

		if ($press->getSetting($setting)) {
			$userGroupDao = DAORegistry::getDAO('UserGroupDAO');
			$userGroup = $userGroupDao->getDefaultByRoleId($press->getId(), $roleId);
			$userGroupDao->assignUserToGroup($user->getId(), $userGroup->getId());
			$request->redirectUrl($request->getUserVar('source'));
		} else {
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->assign('message', $deniedKey);
			return $templateMgr->display('common/message.tpl');
		}
	}
}

?>
