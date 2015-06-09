

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#localFilesForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/customLocale/css/customLocale.css" type="text/css" />

{url|assign:actionUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler" op="updateLocale" locale=$locale key=$filePath anchor="localeContents" escape=false}

<form class="pkp_form" id="localFilesForm" method="post" action="{$actionUrl}">

<h3>{translate key="plugins.generic.customLocale.file.edit" filename=$filePath}</h3>


<table class="listing" width="100%">

	<tr><td colspan="3" class="headseparator">&nbsp;</td></tr>
	<tr class="heading" valign="bottom">
		<td width="35%">{translate key="plugins.generic.customLocale.localeKey"}</td>
		<td width="60%">{translate key="plugins.generic.customLocale.localeKeyValue"}</td>
	</tr>
	<tr><td colspan="2" class="headseparator">&nbsp;</td></tr>


{iterate from=referenceLocaleContents key=key item=referenceValue}
{assign var=filenameEscaped value=$filename|escape:"url"|escape:"url"}
	<tr valign="top"{if $key == $searchKey} class="highlight"{/if}>
		<td class="input">{$key|escape}</td>
		<td class="input">
			<input type="hidden" name="changes[]" value="{$key|escape}" />
			{if $localeContents != null}{assign var=value value=$localeContents.$key}{else}{assign var=value value=''}{/if}
			{if ($value|explode:"\n"|@count > 1) || (strlen($value) > 80) || ($referenceValue|explode:"\n"|@count > 1) || (strlen($referenceValue) > 80)}
				{translate key="plugins.generic.customLocale.file.reference"}<br/>
				<textarea name="junk[]" class="textArea default" rows="5" cols="50" onkeypress="return (event.keyCode >= 37 && event.keyCode <= 40);">
{$referenceValue|escape}
</textarea>
				{translate key="plugins.generic.customLocale.file.custom"}<br/>
				<textarea name="changes[]" {if $value}class="textField test"{else}class="textArea"{/if} rows="5" cols="50">
{$value|escape}
</textarea>
			{else}
				{translate key="plugins.generic.customLocale.file.reference"}<br/>
				<input name="junk[]" class="textField default" type="text" size="50" onkeypress="return (event.keyCode >= 37 && event.keyCode <= 40);" value="{$referenceValue|escape}" /><br/>
				{translate key="plugins.generic.customLocale.file.custom"}<br/>
				<input name="changes[]" {if $value}class="textField test"{else}class="textField"{/if} type="text" size="50" value="{$value|escape}" />
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2" class="{if $referenceLocaleContents->eof()}end{/if}separator">&nbsp;</td>
	</tr>
{/iterate}

</table>

	{fbvFormSection class="formButtons"}
	{**	{fbvElement type="button" class="pkp_helpers_align_left" id="previewButton" label="common.preview"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}**}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}


</form>



























