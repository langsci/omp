{**
 * notLoggedIn.tpl
 *
 * Copyright (c) 2015 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * main template for the public profiles plugin
 *
 *}
{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/publicProfiles/css/publicProfiles.css" type="text/css" />

{translate|assign:"content" key="plugins.generic.publicProfiles.message"}

<div id="publicProfiles">
<br>
<p>{eval var=$content}</p>

</div>

{strip}
	{if !$contentOnly}
		{include file="common/footer.tpl"}
	{/if}
{/strip}
