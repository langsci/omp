{**
 * templates/submission/form/step1.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Step 1 of author submission process.
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#submitStep1Form').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="submitStep1Form" method="post" action="{url op="saveStep" path=$submitStep}">
{if $submissionId}<input type="hidden" name="submissionId" value="{$submissionId|escape}"/>{/if}
	<input type="hidden" name="submissionChecklist" value="1"/>

	{** Carola Fanselow, Anmerkung: leer **}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="submitStep1FormNotification"}


	{fbvFormArea id="submissionStep1"}

		{** Carola Fanselow, Anmerkung:Authors who publish with this press agree to the following terms: **}
		{if $copyrightNoticeAgree}

			{$copyrightNotice}

			{fbvFormSection list="true"}
				{fbvElement type="checkbox" id="copyrightNoticeAgree" required=true value=1
				label="submission.submit.copyrightNoticeAgree" checked=$submissionId}
			{/fbvFormSection}
		{/if}

		{** Carola Fanselow, Anmerkung: Type of editorial workflow **}
		{$additionalFormContent1}

		{** Carola Fanselow, Anmerkung: Submission Language **}
		{if count($supportedSubmissionLocaleNames) == 1}
		{* There is only one supported submission locale; choose it invisibly *}
			{foreach from=$supportedSubmissionLocaleNames item=localeName key=locale}
				{fbvElement type="hidden" id="locale" value=$locale}
			{/foreach}
			{else}
			{* There are several submission locales available; allow choice *}
			{fbvFormSection title="submission.submit.submissionLocale" size=$fbvStyles.size.MEDIUM for="locale"}
				{fbvElement label="submission.submit.submissionLocaleDescription" required="true"
				type="select" id="locale" from=$supportedSubmissionLocaleNames selected=$locale translate=false}
			{/fbvFormSection}
		{/if}{* count($supportedSubmissionLocaleNames) == 1 *}

		{** Carola Fanselow, Anmerkung: Select series **}
		{$additionalFormContent2}

		{** Carola Fanselow: Veränderung: $canExpedite immer auf false -> submission checklist wird immer angezeigt **}
		{assign var="canExpedite" value=false}

		{if $canExpedite}
			<div class="pkp_form_hidden">
		{/if}

		<!-- Submission checklist -->
	
		{if $currentContext->getLocalizedSetting('submissionChecklist')}
		{fbvFormSection list="true" label="submission.submit.submissionChecklist"
					 description="submission.submit.submissionChecklistDescription" id="pkp_submissionChecklist"}
			{foreach name=checklist from=$currentContext->getLocalizedSetting('submissionChecklist')
					key=checklistId item=checklistItem}
				{if $checklistItem.content}
					{** Carola Fanselow: value hinter else: auf false statt auf $submission_id, unklar, ggf. PKP fragen **}
					{if $canExpedite}
						{assign var="checked" value=true}{else}{assign var="checked" value=false}
					{/if}

						{** alt: {if $canExpedite}{assign var="checked" value=true}{else}
						{assign var="checked" value=$submissionId}{/if} **}

					{fbvElement type="checkbox" id="checklist-$checklistId" required=true value=1
						label=$checklistItem.content translate=false checked=$checked}
				{/if}

			{/foreach}

		{/fbvFormSection}
		{/if}


		{if $canExpedite}
			</div>
		{/if}

		<!-- Cover Note To Editor-->
		{fbvFormSection for="commentsToEditor" title="submission.submit.coverNote"}
			{fbvElement type="textarea" name="commentsToEditor" id="commentsToEditor" value=$commentsToEditor rich=true}
		{/fbvFormSection}
	
		{** Carola Fanselow: Privacy Statement weg
		<!-- Privacy Statement -->
		{fbvFormSection for="privacyStatement" title="submission.submit.privacyStatement"}
			{fbvElement type="textarea" name="privacyStatement" id="privacyStatement" disabled=true
				value=$currentContext->getLocalizedSetting('privacyStatement') rich=true}
		{/fbvFormSection}**}


		{if $submissionProgress > 1}
			{assign var="confirmCancelMessage" value="submission.submit.cancelSubmission"}
		{else}
			{assign var="confirmCancelMessage" value="submission.submit.cancelSubmissionStep1"}
		{/if}

		<!-- Buttons -->
		{** Carola Fanselow: Cancel-Link eingefügt, weil omp-cancel-Link nicht funktioniert **}
		<div id="LSPcancelLink">
			<a href="../..">Cancel</a>
		</div>
		<div id="LSPstep1Buttons">
		{fbvFormButtons id="step1Buttons" submitText="common.saveAndContinue" confirmCancel=$confirmCancelMessage}
		</div>

	{/fbvFormArea}


	{** Carola Fanselow: User Group Auswahl: wird über style im template ausgeblendet, wurde von oben nach unten verlegt,
		damit oben keine Lücke entsteht, es muss da bleiben, damit Standardwert ausgelesen werden kann **}
	<!-- Author user group selection (only appears if user has > 1 author user groups) -->
	{if count($authorUserGroupOptions) > 1}
		{** Carola Fanselow, Auswahl "Submit in my role as ..." weg mit div style="visibility: hidden"**}
		<div style="visibility:hidden; float:right">
		{fbvFormSection label="submission.submit.userGroup" description="submission.submit.userGroupDescription"
				inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="select" id="authorUserGroupId" from=$authorUserGroupOptions translate=false}
		{/fbvFormSection}
		</div>
	{else}
		{foreach from=$authorUserGroupOptions key="key" item="authorUserGroupName"}
			{assign var=authorUserGroupId value=$key}
		{/foreach}
		{fbvElement type="hidden" id="authorUserGroupId" value=$authorUserGroupId}
	{/if}

</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
