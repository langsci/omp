{**
 * plugins/generic/identificationCodes/templates/settings.tpl
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * The basic setting for the Identification code plugin.
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#identificationCodesSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="identificationCodesSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="importexport" plugin=$pluginName verb="settings" tab="basic" save="true"}">

	<input type="hidden" name="tab" value="settings"/>

	{fbvFormArea id="identificationCodes" class="border" title="plugins.importexport.identificationCodes.settings.title"}

		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.importexport.identificationCodes.intro.codes"}</p>
			{fbvElement type="textArea" label="plugins.importexport.identificationCodes.lable.codes" required="false" id="langsci_identificationCodes_codes" value=$langsci_identificationCodes_codes maxlength="200" size=$fbvStyles.size.MEDIUM}

		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="langsci_identificationCodes_display" value="1" checked=$langsci_identificationCodes_display label="plugins.importexport.identificationCodes.form.display"}

		{/fbvFormSection}

		{fbvFormButtons submitText="common.save"}

	{/fbvFormArea}

</form>

