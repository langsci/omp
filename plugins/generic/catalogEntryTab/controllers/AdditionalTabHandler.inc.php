<?php

/**
 * @file controllers/tab/catalogEntry/CatalogEntryTabHandler.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CatalogEntryTabHandler
 * @ingroup controllers_tab_catalogEntry
 *
 * @brief Handle AJAX operations for tabs on the New Catalog Entry management page.
 */

// Import the base Handler.
import('lib.pkp.controllers.tab.publicationEntry.PublicationEntryTabHandler');

class AdditionalTabHandler extends PublicationEntryTabHandler {

	/** @var  */
	static $plugin;

	/**
	 * Set the plugin.
	 * @param $plugin 
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Constructor
	 */
	function AdditionalTabHandler() {

		parent::PublicationEntryTabHandler();

		$this->addRoleAssignment(
			array(ROLE_ID_SUB_EDITOR, ROLE_ID_MANAGER),
			array(
				'displayTabContent',
				'saveForm',
			)
		);
	}


	//
	// Public handler methods
	//

	function saveForm($args, $request) {

		$json = new JSONMessage();
		$form = null;

		$submission = $this->getSubmission();
		$stageId = $this->getStageId();
		$notificationKey = null;

		$this->_getFormFromCurrentTab($form, $notificationKey, $request);


		if ($form) { // null if we didn't have a valid tab

			$form->readInputData();

			if($form->validate()) {
				$form->execute($request);

				// Create trivial notification in place on the form
				$notificationManager = new NotificationManager();
				$user = $request->getUser();
				$notificationManager->createTrivialNotification($user->getId(), NOTIFICATION_TYPE_SUCCESS, array('contents' => __($notificationKey)));
			} else {
				// Could not validate; redisplay the form.
				$json->setStatus(true);
				$json->setContent($form->fetch($request));
			}

			if ($request->getUserVar('displayedInContainer')) {

				$router = $request->getRouter();
				$dispatcher = $router->getDispatcher();
				$url = $dispatcher->url($request, ROUTE_COMPONENT, null, $this->_getHandlerClassPath(), 'fetch', null, array('submissionId' => $submission->getId(), 'stageId' => $stageId, 'tabPos' => 2, 'hideHelp' => true));

				$json->setAdditionalAttributes(array('reloadContainer' => true, 'tabsUrl' => $url));
				$json->setContent(true); // prevents modal closure
				return $json->getString();
			} else {
				return $json->getString(); // closes the modal
			}
		} else {
			fatalError('Unknown or unassigned format id!');
		}
	}


	function displayTabContent($args, $request) {

		import('plugins.generic.catalogEntryTab.form.AdditionalTabForm');

		$submission = $this->getSubmission();
		$stageId = $this->getStageId();
		$user = $request->getUser();

		$additionalTabForm = new AdditionalTabForm($submission->getId(), $user->getId(), $stageId, array('displayedInContainer' => true));
		$additionalTabForm->initData($args, $request);
		$json = new JSONMessage(true, $additionalTabForm->fetch($request));

		return $json->getString();
	}

	/**
	 * Get the form for a particular tab.
	 */
	function _getFormFromCurrentTab(&$form, &$notificationKey, $request) {
		parent::_getFormFromCurrentTab($form, $notificationKey, $request); // give PKP-lib a chance to set the form and key.

		if (!$form) { // nothing applicable in parent.
			$submission = $this->getSubmission();

			switch ($this->getCurrentTab()) {
				case 'additionalTab':

					import('plugins.generic.catalogEntryTab.form.AdditionalTabForm');
					$user = $request->getUser();
					$form = new AdditionalTabForm($submission->getId(), $user->getId(), $this->getStageId(), array('displayedInContainer' => true, 'tabPos' => $this->getTabPosition()));
					$notificationKey = 'plugins.generic.catalogEntryTab.notification.addtionalDataSaved';
					//SubmissionLog::logEvent($request, $submission, SUBMISSION_LOG_CATALOG_METADATA_UPDATE, 'submission.event.catalogMetadataUpdated');
					break;
			}
		}
	}

	/**
	 * return a string to the Handler for this modal.
	 * @return String
	 */
	function _getHandlerClassPath() {
		return 'modals.submissionMetadata.CatalogEntryHandler';
	} 
}

?>
