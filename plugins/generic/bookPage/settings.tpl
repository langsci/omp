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
	
		{* Choose social media services *}
		{fbvFormSection list="true"}
			<p class="pkp_help"></p> 
			
			{fbvElement type="checkbox" name="facebook" id="facebook" label="plugins.generic.bookPage.form.facebook" checked=$facebook content=$content inline=true}
		
			{fbvElement type="checkbox" name="twitter" id="twitter" label="plugins.generic.bookPage.form.twitter" checked=$twitter content=$content inline=true}
		
			{fbvElement type="checkbox" id="googleplus" label="plugins.generic.bookPage.form.googleplus" checked=$googleplus content=$content inline=true}
			
			{fbvElement type="checkbox" id="info" label="plugins.generic.bookPage.form.info" checked=$info content=$content inline=true}
			
		{/fbvFormSection}
	
		{* Choose Language *}
		{fbvFormSection label="plugins.generic.bookPage.settings.language"}
			
			{fbvElement type="select"  id="selectedLanguage" from=$languages selected=$selectedLanguage size=$fbvStyles.size.MEDIUM}
		
		{/fbvFormSection}
		
		{* Choose theme *}
		{fbvFormSection label="plugins.generic.bookPage.settings.theme" }
	
			{fbvElement type="select" id="selectedTheme" from=$themes selected=$selectedTheme size=$fbvStyles.size.MEDIUM}
			
		{/fbvFormSection}
		
		{* Add backend url *}
		{fbvFormSection label="plugins.generic.bookPage.settings.backend"}
			<p class="pkp_help">{translate key="plugins.generic.bookPage.settings.backend.info"}</p> 
			
			{fbvElement type="text" id="backend" value=$backend maxlength="100" size=$fbvStyles.size.MEDIUM}
			
		{/fbvFormSection}
		
		
	{/fbvFormArea}
	
	{fbvFormButtons submitText="common.save"}

</form>

