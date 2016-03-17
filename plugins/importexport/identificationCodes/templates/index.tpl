{**
 * plugins/importexport/native/templates/index.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * List of operations this plugin can perform
 *}
{strip}
{include file="common/header.tpl"}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/importexport/identificationCodes/css/identificationCodes.css" type="text/css"/>

<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#importExportTabs').pkpHandler('$.pkp.controllers.TabHandler', {ldelim}
			notScrollable: true
		{rdelim});
		$('#importExportTabs').tabs('option', 'cache', true);
	{rdelim});
</script>

{if !$errorMessage}

<div id="importExportTabs">

	<ul>
		<li><a href="#export-tab">{translate key="plugins.importexport.identificationCodes.export"}</a></li>
		<li><a href="#import-tab">{translate key="plugins.importexport.identificationCodes.import"}</a></li>
	</ul>

	<div id="import-tab">

		<script type="text/javascript">
			$(function() {ldelim}
				// Attach the form handler.
				$('#importXmlForm').pkpHandler('$.pkp.controllers.form.FileUploadFormHandler',
					{ldelim}
						$uploader: $('#plupload'),
							uploaderOptions: {ldelim}
								uploadUrl: '{plugin_url path="uploadImportXML"}',
								baseUrl: '{$baseUrl|escape:javascript}'
							{rdelim}
					{rdelim}
				);
			{rdelim});
		</script>

		<form id="importXmlForm" class="pkp_form" action="{plugin_url path="importBounce"}" method="post">
			{fbvFormArea id="importForm"}
				{* Container for uploaded file *}
				<input type="hidden" name="temporaryFileId" id="temporaryFileId" value="" />
				<p>{translate key="plugins.importexport.identificationCodes.import.instructions"}</p>

				{fbvFormArea id="file"}
					{fbvFormSection title="common.file"}
						{include file="controllers/fileUploadContainer.tpl" id="plupload"}
					{/fbvFormSection}
				{/fbvFormArea}

<button aria-disabled="false" role="button" class="submitFormButton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" id="submitFormButtonCheck" name="submitFormButton1" translate="1"><span class="ui-button-text">Check</span></button>
<button aria-disabled="false" role="button" class="submitFormButton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" id="submitFormButtonImport" name="submitFormButton2" translate="1"><span class="ui-button-text">Import</span></button>


			{/fbvFormArea}
		</form>

	</div>

	<div id="export-tab">

		<script type="text/javascript">
			$(function() {ldelim}
				// Attach the form handler.
				$('#exportXmlForm').pkpHandler('$.pkp.controllers.form.FormHandler');
			{rdelim});
		</script>

		<div id="identificationCodes">

			{if $selectedIdentificationCodes}

				<p>{translate key="plugins.importexport.identificationCodes.introduction"}</p>

				<ul>
					{foreach from=$selectedIdentificationCodes item=code}
					<li>{$onixCodes[$code]}</li>
					{/foreach}
				</ul>


				<form id="exportXmlForm" class="pkp_form" action="{plugin_url path="export"}" method="post">
					{fbvFormArea id="exportForm"}
						<p>{translate key="plugins.importexport.identificationCodes.export.instructions"}</p>
						{fbvFormButtons hideCancel="true"}
					{/fbvFormArea}
				</form>

				{if $displaySettings}

				<table class="identificationCodesTable">
					<tr>
						<th>Sub Id</th>
						<th>Title</th>
						<th>PubFormat</th>
						{foreach from=$selectedIdentificationCodes item=code}
						<th>{$onixCodes[$code]}</th>
						{/foreach}
					</tr>
					{foreach from=$identificationCodes item=identificationCode}
					<tr>
						<td>{$identificationCode.subId}</td>
						<td>{$identificationCode.title}</td>
						<td>{$identificationCode.publicationFormat}</td>
						{foreach from=$selectedIdentificationCodes item=code}
						<td>{$identificationCode[$code]}</td>
						{/foreach}
					</tr>
					{/foreach}
				</table>
				<br>
				{/if}
			
			{else}

				<p>{translate key="plugins.importexport.identificationCodes.noCodesSelected"}</p>

			{/if}

		</div>

	</div>
</div>



{else}
	{$errorMessage}
{/if}

{include file="common/footer.tpl"}
