
{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/annotations/css/annotations.css" type="text/css" />

<div id="annotations">

<p>List of books that are open to annotations. Click on the buttons to get statistics.</p>
<br>


<form method="post" action="file">

    <input type="hidden"  name="file" value="http://langsci-press.org/public/site/pdf/MuellerOpenReview1.pdf">
	
	<div class="annotationFile">
		<a href="https://via.hypothes.is/http://langsci-press.org/public/site/pdf/MuellerOpenReview1.pdf">Grammatical theory (Part 1)</a>
			<button id="buttonRegister" name="buttonRegister" type="submit"
					class="submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button" style="float:right;" >
					<span class="ui-button-text">Details</span>
			</button>
	</div>

</form>

<form method="post" action="file">

    <input type="hidden"  name="file" value="http://langsci-press.org/public/site/pdf/MuellerOpenReview2.pdf">
	
	<div class="annotationFile">
		<a href="https://via.hypothes.is/http://langsci-press.org/public/site/pdf/MuellerOpenReview2.pdf">Grammatical theory (Part 2)</a>
			<button id="buttonRegister" name="buttonRegister" type="submit"
					class="submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button" style="float:right;" >
					<span class="ui-button-text">Details</span>
			</button>
	</div>

</form>

{if $filesFound}

	{assign var=count value=0}
		
	{foreach from=$urlTails item=tail}

		{**<br>{$baseUrl}/index.php/LangSci-Press/catalog/view{$tail}<br>**}
		{**CURLOPT_URL => 'https://hypothes.is/api/search?uri=http://langsci-press.org/catalog/view/83/89/307-1&limit=300' http://test.langsci-press.org/about/contact**}
		{**		https://via.hypothes.is/http://dgp07.hpsg.fu-berlin.de:9085/omp11b/index.php/LangSci/catalog/view 
						https://via.hypothes.is/http://langsci-press.org/catalog/view/75/22/295-1**}

			<form method="post" action="file">
				<div class="annotationFile">	

					<a href="https://via.hypothes.is/{$baseUrl}/index.php/LangSci-Press/catalog/view{$tail}">{$titles[$count]}</a>

					<button id="buttonRegister" name="buttonRegister" type="submit"
						class="submitFormButton button ui-button ui-widget ui-state-default
						ui-corner-all ui-button-text-only" role="button" style="float:right;" >
						<span class="ui-button-text">Details</span>
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
