{**
 * plugins/generic/bookPage/templates/settingsForm.tpl
 *
 * Author: Svantje Lilienthal, Language Science Press, Freie Universität Berlin
 * Last update: August 21, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Book Page plugin settings
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#bookPageSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="bookPageSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" save="true"}">

	{include file="common/formErrors.tpl"}
	
	{fbvFormArea id="bookPagePubFormats" title="plugins.generic.bookPage.manager.settings.pubFormats" class="border"}
	
		{fbvFormSection description="plugins.generic.bookPage.manager.settings.pubFormats.description"}
		{/fbvFormSection}
		
		{fbvFormSection title="plugins.generic.bookPage.manager.settings.forthcoming"  for="bookPageForthcoming" size=$fbvStyles.size.MEDIUM}
			{fbvElement label="plugins.generic.bookPage.manager.settings.forthcoming.label" type="text" name="bookPageForthcoming" id="bookPageForthcoming" value=$bookPageForthcoming}
		{/fbvFormSection}
		
		{fbvFormSection title="plugins.generic.bookPage.manager.settings.review"  for="bookPageReview" size=$fbvStyles.size.MEDIUM}
			{fbvElement label="plugins.generic.bookPage.manager.settings.review.label" type="text" name="bookPageReview" id="bookPageReview" value=$bookPageReview}
		{/fbvFormSection}
		
		{fbvFormSection title="plugins.generic.bookPage.manager.settings.download"  for="bookPageDownload" size=$fbvStyles.size.MEDIUM}
			{fbvElement label="plugins.generic.bookPage.manager.settings.download.label"  type="text" name="bookPageDownload" id="bookPageDownload" value=$bookPageDownload}
		{/fbvFormSection}
		
	{/fbvFormArea}
	
	{fbvFormButtons submitText="common.save"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
