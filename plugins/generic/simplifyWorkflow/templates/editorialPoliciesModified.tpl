{**
 * plugins/generic/topnavi/templates/editorialPolicies.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}

{strip}
{assign var="pageTitle" value="about.editorialPolicies"}
{include file="common/header.tpl"}
{/strip}

{url|assign:editUrl page="management" op="settings" path="press" anchor="policies"}
{include file="common/linkToEditPage.tpl" editUrl=$editUrl}

{** copied from submission.tpl**}
{if $submissionInfo.copyrightNotice}
	<div id="copyrightNotice">
		<h3>{translate key="about.copyrightNotice"}</h3>

		{url|assign:editUrl page="management" op="settings" path="press" anchor="policies"}
		{include file="common/linkToEditPage.tpl" editUrl=$editUrl}

		<div>{$submissionInfo.copyrightNotice|nl2br}</div>
	</div>
	<div class="separator"></div>
{/if}


{** copied from submission.tpl**}
{if $submissionInfo.privacyStatement}
	<div id="privacyStatement">
		<h3>{translate key="about.privacyStatement"}</h3>

		{url|assign:editUrl page="management" op="settings" path="press" anchor="policies"}
		{include file="common/linkToEditPage.tpl" editUrl=$editUrl}

		<p>{$submissionInfo.privacyStatement|nl2br}</p>
	</div>
	<div class="separator"></div>
{/if}

{if !empty($editorialPoliciesInfo.reviewPolicy)}
	<div id="peerReviewProcess">
		<h3>{translate key="about.reviewPolicy"}</h3>
		<p>{$editorialPoliciesInfo.reviewPolicy|nl2br}</p>
	</div>
	<div class="separator"></div>
{/if}

{if !empty($editorialPoliciesInfo.openAccessPolicy)}
	<div id="openAccessPolicy">
		<h3>{translate key="about.openAccessPolicy"}</h3>
		<p>{$editorialPoliciesInfo.openAccessPolicy|nl2br}</p>
	</div>
	<div class="separator"></div>
{/if}

{foreach key=key from=$editorialPoliciesInfo.customAboutItems item=customAboutItem name=customAboutItems}
	{if !empty($customAboutItem.title)}
		<div id="custom-{$key|escape}"><h3>{$customAboutItem.title|escape}</h3>
			<p>{$customAboutItem.content|nl2br}</p>
		</div>
		{if !$smarty.foreach.customAboutItems.last}<div class="separator"></div>{/if}
	{/if}
{/foreach}

{include file="common/footer.tpl"}
