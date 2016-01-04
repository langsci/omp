{**
 * plugins/generic/annotations/annotations.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/annotations/css/annotations.css" type="text/css" />

<div id="annotations">

<p>{translate key="plugins.generic.annotations.intro"}</p><br>

{** The Müller documents are exceptions and are therefor hardcoded **}
<form method="post" action="annotationFile">

	<input type="hidden"  name="file" value="http://langsci-press.org/public/site/pdf/MuellerOpenReview1.pdf">
	<input type="hidden"  name="title" value="Grammatical theory - MuellerOpenReview1">
	
	<div class="annotationFile">
		<a href="https://via.hypothes.is/http://langsci-press.org/public/site/pdf/MuellerOpenReview1.pdf">Grammatical theory - MuellerOpenReview1</a>
			<button type="submit"
					class="buttonAnnotationStatistics submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button" >
					<span class="ui-button-text">Statistics</span>
			</button>
	</div>

</form>

{** The Müller documents are exceptions and are therefor hardcoded **}
<form method="post" action="annotationFile">

	<input type="hidden"  name="file" value="http://langsci-press.org/public/site/pdf/MuellerOpenReview2.pdf">
	<input type="hidden"  name="title" value="Grammatical theory - MuellerOpenReview2">
	
	<div class="annotationFile">
		<a href="https://via.hypothes.is/http://langsci-press.org/public/site/pdf/MuellerOpenReview2.pdf">Grammatical theory - MuellerOpenReview2</a>
			<button type="submit"
					class="buttonAnnotationStatistics submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button">
					<span class="ui-button-text">Statistics</span>
			</button>
	</div>

</form>

{** all files currently online **}
{if $onlineFilesFound}

	{assign var=count value=0}
		
	{foreach from=$onlineUrlTails item=tail}

			<form method="post" action="annotationFile">

				<input type="hidden"  name="file" value="{$urlPrefix}{$serverHost}/catalog/view{$tail}">
				<input type="hidden"  name="title" value="{$onlineTitles[$count]}">

				<div class="annotationFile">	

					<a href="https://via.hypothes.is/{$serverHost}/catalog/view{$tail}">{$onlineTitles[$count]}</a>

					<button  type="submit"
						class="buttonAnnotationStatistics submitFormButton button ui-button ui-widget ui-state-default
						ui-corner-all ui-button-text-only" role="button">
						<span class="ui-button-text">Statistics</span>
					</button>
				</div>	
			</form>

			{assign var=count value=$count+1}

	{/foreach}

{/if}

{** all files that are not online anymore **}

{if $offlineFilesFound}

<h2 class="title_left">Offline files</h2><br>

	{assign var=count value=0}
		
	{foreach from=$offlineUrlTails item=tail}

			<form method="post" action="annotationFile">

				<input type="hidden"  name="file" value="{$urlPrefix}{$serverHost}/catalog/view{$tail}">
				<input type="hidden"  name="title" value="{$offlineTitles[$count]}">

				<div class="annotationFile">	

					<a href="https://via.hypothes.is/{$serverHost}/catalog/view{$tail}">{$offlineTitles[$count]}</a>

					<button  type="submit"
						class="buttonAnnotationStatistics submitFormButton button ui-button ui-widget ui-state-default
						ui-corner-all ui-button-text-only" role="button">
						<span class="ui-button-text">Statistics</span>
					</button>
				</div>	
			</form>

			{assign var=count value=$count+1}

	{/foreach}

{/if}

</div>

{strip}
		{include file="common/footer.tpl"}
{/strip}
