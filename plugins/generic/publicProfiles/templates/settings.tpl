{**
 * plugins/generic/publicProfiles/templates/settings.tpl
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting tab for the Public profiles plugin.
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#publicProfilesSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="publicProfilesSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings" />

	{fbvFormArea id="settingsForm" class="border" title="plugins.generic.publicProfiles.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.publicProfiles.settings.pathIntro"}</p>
			{fbvElement type="text" label="plugins.generic.publicProfiles.settings.path" required="false" id="langsci_publicProfiles_path" value=$langsci_publicProfiles_path maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.publicProfiles.settings.userGroupsIntro"}</p>
			{fbvElement type="text" label="plugins.generic.publicProfiles.settings.userGroups" required="false" id="langsci_publicProfiles_userGroups" value=$langsci_publicProfiles_userGroups maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="langsci_publicProfiles_onlyPublishedMonographs" value="1" checked=$langsci_publicProfiles_onlyPublishedMonographs label="plugins.generic.publicProfiles.settings.onlyPublishedMonographs"}
			{fbvElement type="checkbox" id="langsci_publicProfiles_unifiedStyleSheetForLinguistics" value="1" checked=$langsci_publicProfiles_unifiedStyleSheetForLinguistics label="plugins.generic.publicProfiles.settings.unifiedStyleSheetForLinguistics"}
		{/fbvFormSection}


		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}

</form>

