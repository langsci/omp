{**
 * templates/about/editorialPolicies.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * About the Press / Editorial Policies.
 *}


{strip}
{assign var="pageTitle" value="about.editorialPolicies"}
{include file="common/header.tpl"}
{/strip}

{url|assign:editUrl page="management" op="settings" path="press" anchor="policies"}
{include file="common/linkToEditPage.tpl" editUrl=$editUrl}


<br>
	<h3>{translate key="about.copyrightNotice"}</h3>

<p>Authors who publish with this press agree to the following terms:</p><ol><li>Authors retain copyright and grant the press right of first publication with the work simultaneously licensed under a <a href="http://creativecommons.org/licenses/by/3.0/" target="_blank">Creative Commons Attribution License</a> that allows others to share the work with an acknowledgement of the work's authorship and initial publication in this press.</li><li>Authors are able to enter into separate, additional contractual series for the non-exclusive distribution of the version of the work published by the press (e.g., post it to an institutional repository or publish it in a book), with an acknowledgement of its initial publication in this press.</li><li>Authors are permitted and encouraged to post their work online (e.g., in institutional repositories or on their website) prior to and during the submission process, as it can lead to productive exchanges, as well as earlier and greater citation of published work (See <a href="http://opcit.eprints.org/oacitation-biblio.html" target="_blank">The Effect of Open Access</a>).</li></ol>

<br>
<h3>{translate key="about.privacyStatement"}</h3>

<p>The names and email addresses entered in this press site will be used exclusively for the stated purposes of this press and will not be made available for any other purpose or to any other party.</p>

<br>

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
