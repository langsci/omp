{**
 * plugins/generic/groupMail/groupMail.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *$identificationCodes[$subId][$code] = $this->convertFromDB($row['value']);
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/identificationCodes/css/identificationCodes.css" type="text/css" />

<p>{translate key="plugins.generic.identificationCodes.introduction"}</p>

<p>Selected code ids: 
{foreach from=$selectedIdentificationCodes item=code}
	{$code}
{/foreach}
(see locale/en_US/ONIX_BookProduct_CodeLists.xsd)
</p>

<div id="identificationCodes">
<br>
	<table>
		<tr>
			<th>SubmId</th>
			<th>Title</th>
			{foreach from=$selectedIdentificationCodes item=code}
			<th>{$onixCodes[$code]}</th>
			{/foreach}
		</tr>
		{foreach from=$identificationCodes item=identificationCode}
		<tr>
			<td>{$identificationCode.subId}</td>
			<td>{$identificationCode.title}</td>
			{foreach from=$selectedIdentificationCodes item=code}
			<td>{$identificationCode[$code]}</td>
			{/foreach}
		</tr>
		{/foreach}

</table> 



</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
