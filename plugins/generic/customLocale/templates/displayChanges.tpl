{**
 * plugins/generic/hallOfFame/hallOfFame.tpl
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * main template for the hall of fame plugin
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<p>{translate key="plugins.generic.customLocale.fileSaved"}
{$customLocaleDir}/{translate key="plugins.generic.customLocale.fileName"}.txt.</p>


{strip}
		{include file="common/footer.tpl"}
{/strip}

