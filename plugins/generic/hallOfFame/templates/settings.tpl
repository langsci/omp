{**
 * plugins/generic/addThis/settings.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting tab for the AddThis plugin.
 *}


<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#addThisPluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="addThisPluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings" />

	{fbvFormArea id="myText" class="border" title="plugins.generic.hallOfFame.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">Testfeld:</p>
			{fbvElement type="text" label="plugins.generic.hallOfFame.form.userGroupsHallOfFame" required="false" id="userGroupsHallOfFame" value=$userGroupsHallOfFame maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection}
			<p class="pkp_help">Testfeld:</p>
			{fbvElement type="text" label="plugins.generic.hallOfFame.form.pathHallOfFame" required="false" id="pathHallOfFame" value=$pathHallOfFame maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="onlyPublishedMonographs" value="1" checked=$onlyPublishedMonographs label="plugins.generic.hallOfFame.form.onlyPublishedMonographs"}
			{fbvElement type="checkbox" id="linksToPublicProfile" value="1" checked=$linksToPublicProfile label="plugins.generic.hallOfFame.form.linksToPublicProfile"}
			{fbvElement type="checkbox" id="unifiedStyleSheetForLinguistics" value="1" checked=$unifiedStyleSheetForLinguistics label="plugins.generic.hallOfFame.form.unifiedStyleSheetForLinguistics"}
		{/fbvFormSection}


		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}



</form>
