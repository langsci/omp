
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#localFilesForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<script type="text/javascript">

	var searchString = "{$searchString}";


	{literal}

		function checkKey() {
			document.getElementById("searchKey").checked = true;
		}

		if (document.getElementById(searchString)) {
			document.getElementById(searchString).scrollIntoView(false);
		}

	{/literal}
</script>

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/customLocale/css/customLocale.css" type="text/css" />



{url|assign:actionUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler" op="updateLocale" currentPage=$currentPage locale=$locale key=$filePath  anchor="localeContents" escape=false}

<form class="pkp_form" id="localFilesForm" method="post" action="{$actionUrl}">

<h3>{translate key="plugins.generic.customLocale.file.edit" filename=$filePath}</h3>
<br>
<input type="checkbox" style="display:none" name="searchKey" id="searchKey">

<label></label>
<input type="text" name="searchString" id="searchString" value="{$searchString}">

<button type="submit" onclick="checkKey()" class="submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only">{translate key="plugins.generic.customLocale.search.key"}</button> 
<p>{translate key="plugins.generic.customLocale.seasrchDescription"}</p>


<br><br>


<table class="listing" width="100%">

	<tr><td colspan="3" class="headseparator">&nbsp;</td></tr>
	<tr class="heading" valign="bottom">
		<td width="35%">{translate key="plugins.generic.customLocale.localeKey"}</td>
		<td width="60%">{translate key="plugins.generic.customLocale.localeKeyValue"}</td>
	</tr>
	<tr><td colspan="2" class="headseparator">&nbsp;</td></tr>

{iterate from=referenceLocaleContents key=key item=referenceValue}
{assign var=filenameEscaped value=$filename|escape:"url"|escape:"url"}
	<tr valign="top"{if $key == $searchString} class="highlight"{/if}>
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
				<textarea name="changes[]" id="{$key|escape}" {if $value}class="textField valueChanged"{else}class="textArea"{/if} rows="5" cols="50">
{$value|escape}
</textarea>
			{else}
				{translate key="plugins.generic.customLocale.file.reference"}<br/>
				<input name="junk[]" class="textField default" type="text" size="50" onkeypress="return (event.keyCode >= 37 && event.keyCode <= 40);" value="{$referenceValue|escape}" /><br/>
				{translate key="plugins.generic.customLocale.file.custom"}<br/>
				<input name="changes[]" id="{$key|escape}" {if $value}class="textField valueChanged" {else}class="textField"{/if} type="text" size="50" value="{$value|escape}" />
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2" class="{if $key == $searchString}highlight{/if} {if $referenceLocaleContents->eof()}end{/if} separator">&nbsp;</td>
	</tr>
{/iterate}

</table>

<select name="nextPage" id="nextPage">
{foreach from=$dropdownEntries item=item key=key}
	{assign var="prefix" value=$item|substr:0:4}
	{if $prefix=="stay"}
		<option selected="selected" value={$key}>{$item}</option>
	{else}
		<option value={$key} >{$item}</option>
	{/if}
{/foreach}
</select>

{fbvFormButtons id="submitCustomLocaleFileTemplate" submitText="plugins.generic.customLocale.saveAndContinue"}

 


</form>

























