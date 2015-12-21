{**
 * publicProfile.tpl
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


<div id="publicProfiles">


{if $showProfile}

	<img alt="" src="{$imagePath}"/>
	<h3>Public profile of {$academic_title} {$first_name} {$last_name}</h3><br>
	{if !$affiliation==""}<p><span class="header">Affiliation:</span> {$affiliation}</p>{/if}
	{if $showEmail}<p><span class="header">Email: </span><a href="mailto:{$email}">{$email}</a></p>{/if}
	{if !$url==""}<p><span class="header">Website: </span><a href="{$url}">{$url}</a></p>{/if}
	{if !$biostatement==""}<p><span class="header">Bio Statement:</span></p> {$biostatement}{/if}
	{$bookAchievements}

{else}
	<p>No public profile available.</p>
{/if}


</div>

{strip}
	{if !$contentOnly}
		{include file="common/footer.tpl"}
	{/if}
{/strip}
