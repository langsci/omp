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


<script language="JavaScript" type="text/javascript">
</script> 



<div class="LSP">
<div class="seriesPage">

	<ul>
    	{foreach from=$data item=i}
			<li>{$i.image}{$i.link}</li>
    	{/foreach} 
	</ul>

</div> 
</div> 


{strip}
		{include file="common/footer.tpl"}
{/strip}
