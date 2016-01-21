{**
 * plugins/generic/seriesOverview/settings.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting for the Series Overview plugin.
 *}


<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#seriesOverviewSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="seriesOverviewSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings"/>

	{fbvFormArea id="myText" class="border" title="plugins.generic.seriesOverview.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.generic.seriesOverview.form.pathIntro"}</p>
			{fbvElement type="text" label="plugins.generic.seriesOverview.form.path" required="false" id="langsci_seriesOverview_path" value=$langsci_seriesOverview_path maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="langsci_seriesOverview_useImages" value="1" checked=$langsci_seriesOverview_useImages label="plugins.generic.seriesOverview.form.useImages"}
			{fbvElement type="checkbox" id="langsci_seriesOverview_imageOnSeriesPages" value="1" checked=$langsci_seriesOverview_imageOnSeriesPages label="plugins.generic.seriesOverview.form.imageOnSeriesPages"}
			{fbvElement type="checkbox" id="langsci_seriesOverview_setTabTitle" value="1" checked=$langsci_seriesOverview_setTabTitle label="plugins.generic.seriesOverview.form.setTabTitle"}
		{/fbvFormSection}

		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}

</form>
