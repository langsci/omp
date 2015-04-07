{**
 * plugins/generic/userWebsiteSettings/templates/settings.tpl
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting tab for the User website settings plugin.
 *}


<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#userWebsiteSettingsPluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="userWebsiteSettingsPluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings" />

	{fbvFormArea id="settingsForm" class="border" title="plugins.generic.userWebsiteSettings.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.userWebsiteSettings.settings.pathIntro"}</p>
			{fbvElement type="text" label="plugins.generic.userWebsiteSettings.settings.path" required="false" id="langsci_userWebsiteSettings_path" value=$langsci_userWebsiteSettings_path maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="langsci_userWebsiteSettings_publicProfile" value="1"
				checked=$langsci_userWebsiteSettings_publicProfile label="plugins.generic.userWebsiteSettings.settings.publicProfile"}
			{fbvElement type="checkbox" id="langsci_userWebsiteSettings_email" value="1"
				checked=$langsci_userWebsiteSettings_email label="plugins.generic.userWebsiteSettings.settings.email"}
			{fbvElement type="checkbox" id="langsci_userWebsiteSettings_hallOfFame" value="1"
				checked=$langsci_userWebsiteSettings_hallOfFame label="plugins.generic.userWebsiteSettings.settings.hallOfFame"}
			{fbvElement type="checkbox" id="langsci_userWebsiteSettings_profileImage" value="1"
				checked=$langsci_userWebsiteSettings_profileImage label="plugins.generic.userWebsiteSettings.settings.profileImage"}
		{/fbvFormSection}


		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}


</form>

