{**
 * plugins/generic/seriesPage/page.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2. 
 *
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/seriesPage/css/seriesPage.css" type="text/css" />

<script language="JavaScript" type="text/javascript">
</script> 

<div id="seriesPage">

	<ul>
    	{foreach from=$data item=i}
			<li>{$i.image}{$i.link}</li>
    	{/foreach} 
	</ul>

</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
