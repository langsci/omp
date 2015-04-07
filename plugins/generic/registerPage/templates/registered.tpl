{**
 * plugins/generic/registerPage/templates/registered.tpl
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

<br>
{$content}

{strip}
		{include file="common/footer.tpl"}
{/strip}

