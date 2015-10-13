<?php

/**
 * @file plugins/generic/supporterPage/SupporterPagePlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SupporterPagePlugin
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.mail.MailDAO');
import('classes.monograph.MonographDAO');
import('classes.press.SeriesDAO');

class MailPlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register('Mail::send', array(&$this, 'handleMails'));
				//HookRegistry::register ('LoadHandler', array(&$this, 'handleLoadRequest'));
				HookRegistry::register('submissionsubmitstep3form::validate', array(&$this, 'handleStep3'));
				//HookRegistry::register('PKPNotificationOperationManager::sendNotificationEmail', array(&$this, 'handleNotifications'));

			}
			return true;
		}
		return false;

	}

	function handleStep3($hookName, $args) {

		$mailDAO = new MailDAO;
		$mailDAO->setSubject('{$notificationType}');
		return false;                   
	}

	/*function handleLoadRequest($hookName, $args) {

		$page = $args[0];
		$op = $args[1];

		if ($page == 'mailtest'  && $op == 'mailtest') {

			define('MAIL_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'MailHandler');
			$this->import('MailHandler');
			return true;
		} 
		if ($page == 'mailtest'  && $op == 'delete') {

			$request = $this -> getRequest();
			$press = $request->getPress();
			$press->updateSetting('carolatest','');
			$args[1]='mailtest';
			define('MAIL_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'MailHandler');
			$this->import('MailHandler');
			return true;
		} 
		return false;
	}*/

	function handleNotifications($hookName, $args) {

		$request = $this -> getRequest();
		$notification =& $args[0];
		//$press = $request->getPress();
		//$notification->clearRecipients();

		//if ($notification->getType()==16777247) {
		//	$value = $press->getSetting('carolatest');
		//	$press->updateSetting('carolatest', $value ."-EDAS-");
		//}

		//$value = $press->getSetting('carolatest');
		//$press->updateSetting('carolatest', $value ."-not:". $notification->getType());

		return false;
	}

	function handleMails($hookName, $args) {

		$request = $this -> getRequest();
		$press = $request->getPress();
		$user = $request->getUser();

		$template =& $args[0];
		$subject = $template->getSubject();
		$body = $template->getBody();
		$key = $template->emailKey;

		// set from-address
		$template->setFrom('noreply@langsci-press.org','Language Science Press');

		// set reply-to-address
		if ($user) {
			$template->setReplyTo($user->getEmail(), $user->getFullName());
		} else {
			$template->setReplyTo('snordhoff@langsci-press.org','Sebastian Nordhoff');
		}

		//block emails 
		if ($key=='NOTIFICATION' && !strpos($subject,'16777247')===false)	{				//NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_REQUIRED{  
			$template->setRecipients(array());
		}

		// replace subject 
		if ($key=='NOTIFICATION'){

				$template->setSubject('['.$press->getPath().'] New notification from Language Science Press'); 
				if  (!strpos($subject,'16777217')===false) {
					$template->setSubject('['.$press->getPath().'] A new monograph has been submitted');
				}
		}

		// replace series title in 16777217-notification body
		if ($key=='NOTIFICATION'){
			if  (!strpos($subject,'16777217')===false) {
				$seriesFound=false;
				$tmp1 = substr($body,strpos($body,'workflow/submission/'));
				$tmp2 = substr($tmp1,20);
				$monographId="";
				for ($i=0;$i<strlen($tmp2);$i++) {
					if (ctype_digit($tmp2[$i])) {
						$monographId=$monographId.$tmp2[$i];
					}
				}		
				if (ctype_digit($monographId)) {
					$monographDAO = new MonographDAO;
					$monograph = $monographDAO -> getById($monographId);
					if ($monograph) {
						$seriesDAO = new SeriesDAO;
						$series = $seriesDAO -> getById($monograph->getSeriesId());
						if ($series) {
							$template->assignParams(array('seriesInfo' =>  ' to the series "' . $series ->getLocalizedFullTitle().'"'));
							$seriesFound=true;
						}
					}
				}
				if (!$seriesFound) {
					$template->assignParams(array('seriesInfo' => ''));
				}
			}
		}

		// replace variables in mail bodies and subjects
		$template->assignParams(array('senderName' => $user->getFullName()));

		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.mail.displayName');
	}

	function getDescription() {
		return __('plugins.generic.mail.description');
	}

}

?>
