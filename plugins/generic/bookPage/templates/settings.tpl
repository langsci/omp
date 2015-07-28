{**
 * plugins/generic/bookPage/settings.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting tab for the Book page plugin.
 *}


<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#bookPagePluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="bookPagePluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	{fbvFormArea id="myText" class="border" title="plugins.generic.bookPage.settings.title"}
	
		{* Add name of pubFormat *}
		{fbvFormSection label="plugins.generic.bookPage.settings.pubFormat"}
			<p class="pkp_help">{translate key="plugins.generic.bookPage.settings.pubFormat.info"}</p> 
			
			{fbvElement type="text" id="pubFormat" value=$pubFormat maxlength="100" size=$fbvStyles.size.MEDIUM}
			
		{/fbvFormSection}
		
		
	{/fbvFormArea}
	
	{fbvFormButtons submitText="common.save"}

</form>

