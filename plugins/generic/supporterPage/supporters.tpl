{**
 * plugins/generic/supporterPage/supporters.tpl
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
<div class="SupporterPage">

	<p>{eval var=$intro}</p>
	{$supporter}

</div> {** end of div class="LSP" *}
</div> {** end of div class="SupporterPage" *}

{strip}
		{include file="common/footer.tpl"}
{/strip}
