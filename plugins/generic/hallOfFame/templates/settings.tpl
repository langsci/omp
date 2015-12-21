{**
 * plugins/generic/hallOfFame/settings.tpl
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting tab for the Hall of fame plugin.
 *}


<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#hallOfFamePluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="hallOfFamePluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings"/>

	{fbvFormArea id="myText" class="border" title="plugins.generic.hallOfFame.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.hallOfFame.form.userGroupsIntro"}</p>
			{fbvElement type="text" label="plugins.generic.hallOfFame.form.userGroups" required="false" id="langsci_hallOfFame_userGroups" value=$langsci_hallOfFame_userGroups maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.hallOfFame.form.pathIntro"}</p>
			{fbvElement type="text" label="plugins.generic.hallOfFame.form.path" required="false" id="langsci_hallOfFame_path" value=$langsci_hallOfFame_path maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="langsci_hallOfFame_onlyPublishedMonographs" value="1" checked=$langsci_hallOfFame_onlyPublishedMonographs label="plugins.generic.hallOfFame.form.onlyPublishedMonographs"}
			{fbvElement type="checkbox" id="langsci_hallOfFame_linksToPublicProfile" value="1" checked=$langsci_hallOfFame_linksToPublicProfile label="plugins.generic.hallOfFame.form.linksToPublicProfile"}
			{fbvElement type="checkbox" id="langsci_hallOfFame_unifiedStyleSheetForLinguistics" value="1" checked=$langsci_hallOfFame_unifiedStyleSheetForLinguistics label="plugins.generic.hallOfFame.form.unifiedStyleSheetForLinguistics"}
		{/fbvFormSection}

		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}



</form>
