
{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/annotations/css/annotations.css" type="text/css" />

<div id="annotations">

<p>List of files that are open to annotations (book name - file name). Click on the names to go to the annotation mode. Click on the buttons to get statistics.</p>
<br>


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
