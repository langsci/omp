<?php

/**
 * @file plugins/generic/emailsAndNotifications/EmailsAndNotificationsPlugin.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class EmailsAndNotificationsPlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.emailsAndNotifications.EmailsAndNotificationsDAO');
import('classes.monograph.MonographDAO');
import('classes.press.SeriesDAO');

class EmailsAndNotificationsPlugin extends GenericPlugin {

	const DEFAULTNAME = 'Sebastian Nordhoff'; 
	const DEFAULTADDRESS = 'snordhoff@langsci-press.org'; 
	const NOREPLYNAME = 'Language Science Press'; 
	const NOREPLYADDRESS = 'noreply@langsci-press.org'; 

	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register('Mail::send', array(&$this, 'handleMails'));
				HookRegistry::register('submissionsubmitstep3form::validate', array(&$this, 'handleStep3'));
				//HookRegistry::register('PKPNotificationOperationManager::sendNotificationEmail', array(&$this, 'handleNotifications'));

			}
			return true;
		}
		return false;

	}

	// for all notificiations: put notification type into subject to transfer the variable -> subject MUST be replaced in a later step!
	// todo: add link to PKP-Forum for a discussion about better ways of transfering the notification type
	function handleStep3($hookName, $args) {

		$emailsAndNotificationsDAO = new EmailsAndNotificationsDAO;
		$emailsAndNotificationsDAO->setSubject('{$notificationType}');
		return false;                   
	}

	// handle the hook PKPNotificationOperationManager::sendNotificationEmail
	function handleNotifications($hookName, $args) {

		//$request = $this->getRequest();
		//$notification =& $args[0];
		//$notification->clearRecipients();

		return false;
	}

	function handleMails($hookName, $args) {

		// get general variables
		$request = $this->getRequest();
		$user = $request->getUser();
		$press = $request->getPress();

		// get mail template variables
		$template =& $args[0];
		$subject = $template->getSubject();
		$body = $template->getBody();
		$key = $template->emailKey;

		// construct series info
		$seriesInfo="";
		$monographDAO = new MonographDAO;
		$monograph = $monographDAO->getById($request->getUserVar('submissionId'));
		if ($monograph) {
			$seriesDAO = new SeriesDAO;
			$series = $seriesDAO -> getById($monograph->getSeriesId());
			if ($series) {
				$seriesInfo = ' to the series "' . $series->getLocalizedFullTitle(). '"';
			}
		}

		// set from-address
		$template->setFrom(self::NOREPLYADDRESS,self::NOREPLYNAME);

		// set reply-to-address
		if ($user) {
			$template->setReplyTo($user->getEmail(), $user->getFullName());
		} else {
			$template->setReplyTo(self::DEFAULTADDRESS,self::DEFAULTNAME);
		}

		//-------- block emails --------------------------------------------

		// block NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_REQUIRED (16777247)
		if ($key=='NOTIFICATION' && !strpos($subject,'16777247')===false)	{		 
			$template->setRecipients(array());
		}
		//------------------------------------------------------------------

		// set subject 
		if ($key=='NOTIFICATION'){
			// default
			$template->setSubject('[LangSci-Press] New notification from Language Science Press');
			//$template->setSubject('['.$press->getPath().'] New notification from Language Science Press'); 
			// specific
			if  (!strpos($subject,'16777217')===false) {
				$template->setSubject('[LangSci-Press] A new monograph has been submitted{$seriesInfo}');
			}
		}

		//---- replace variables in mail bodies and subjects ----------------

		if ($user) {
			$template->assignParams(array('senderName' => $user->getFullName()));
		} else {
			$template->assignParams(array('senderName' => self::DEFAULTNAME));
		}

		// Notification "A new monograph has been submitted"
		if ($key=='NOTIFICATION' && !strpos($subject,'16777217')===false){
			$template->assignParams(array('seriesInfo' => $seriesInfo));
		}
		// -----------------------------------------------------------------

		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.emailsAndNotifications.displayName');
	}

	function getDescription() {
		return __('plugins.generic.emailsAndNotifications.description');
	}

}

?>
