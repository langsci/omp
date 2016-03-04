 {**
 * templates/catalog/book/bookInfo.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display the information pane of a public-facing book view in the catalog.
 *}

<script type="text/javascript">

	// Attach the tab handler.
	$(function() {ldelim}
		$('#bookInfoTabs').pkpHandler(
			'$.pkp.controllers.TabHandler',
			{ldelim}
				notScrollable: true
			{rdelim}
		);
	{rdelim});
	
	// accordion
	$(function() {ldelim}
		$(".bookAccordion" ).accordion({ldelim} 
			collapsible: true,  
			active: 0
			 {rdelim});
	{rdelim});
	
	// vg wort
	function addPixel(id){ldelim}
	
		document.getElementById("vgwpixel"+id).innerHTML="<img src='{$imageUrl}' alt=''></img>";
	
	{rdelim}
	
</script>

<!-- css for font awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{$baseUrl}/plugins/generic/bookPage/BookPagePlugin.css" type="text/css" />

<div class="bookInfo">
	<div class="bookInfoHeader">
		<h3>{$publishedMonograph->getLocalizedFullTitle()|strip_unsafe_html}</h3>
		<div class="authorName">{$publishedMonograph->getAuthorString()}</div>
	</div>
	
		<!-- display first document of filelist  -->
		<!-- TODO: get name of publicationFormat that should be displayed here from the settings -->
			
		{if $availableFiles|@count == 0}
		
			 <!-- Forthcoming -->
			<div class="publicationFormatLink forthcoming">{$bookPageForthcoming}</div>
			
		{else}
		
			{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
			{assign var=currency value=$currentPress->getSetting('currency')}
			
			{assign var=completeBook value="false"}
			
			{foreach from=$publicationFormats item=publicationFormat}
			
				<!-- Complete Book -->
				{if $publicationFormat->getIsAvailable() && $publicationFormat->getIsApproved() && $publicationFormat->getLocalizedName()==$bookPageDownload}
				
					{include file="../plugins/generic/bookPage/templates/langsciCompleteBook.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency} 
					
					{assign var=completeBook value="true"}
					
				{/if}
					
			{/foreach}
			
			{if $completeBook=="false"}
			
				{foreach from=$publicationFormats item=publicationFormat}
					
					<!-- Open Review -->
					{if $publicationFormat->getIsAvailable() && $publicationFormat->getIsApproved() && $publicationFormat->getLocalizedName()==$bookPageReview}
						
						{include file="../plugins/generic/bookPage/templates/langsciOpenReview.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency openreviewLink0=$openreviewLink0}
							
					{/if}
						
				{/foreach}
				
			{/if}
			
		{/if}
		
		<div class="clear"></div>
		<br>
		
	<div class="bookAccordion">
	
		<!-- about this book  -->
		<h3 class="accordionHeader"><a href="#">About this book</a></h3>
		<div>
			{assign var=abstract value=$publishedMonograph->getLocalizedAbstract()|strip_unsafe_html}
			{assign var=sentences value='[.]'|split:$abstract:2}
			{$sentences.0}.
			
			{if $sentences.1}
				<br>
				<a id="show" onclick='document.getElementById("more").style.display="block";document.getElementById("show").style.display="none";document.getElementById("less").style.display="block";'>{translate key="plugins.generic.bookPage.more"}</a> 
				<div id="more" class="more">
					{$sentences.1}
				</div>
				<a id="less" class="less" onclick='document.getElementById("more").style.display="none";document.getElementById("show").style.display="block";document.getElementById("less").style.display="none";'>{translate key="plugins.generic.bookPage.less"}</a> 
			{/if}
			
		</div>
	
		<!-- about the authors / volume editor -->
		{assign var=authors value=$publishedMonograph->getAuthors()}
			{foreach from=$authors item=author}
				{if $author->getIncludeInBrowse()}
				
					<h3 class="accordionHeader"><a href="#">About {$author->getFullName()}</a></h3>
					<div>
						{assign var=biography value=$author->getLocalizedBiography()|strip_unsafe_html}
						{if $biography != ''}{$biography}{else}{translate key="catalog.noBioInfo"}{/if}
					</div>
				
				{/if}
			{/foreach}
			
		<!-- chapters  
		{if $publishedMonograph->getWorkType() == WORK_TYPE_EDITED_VOLUME && $chapters|@count != 0}
			
			<h3 class="accordionHeader"><a href="#">Chapters</a></h3>
			<div>
				{foreach from=$chapters item=chapter}
					<strong>{$chapter->getLocalizedTitle()}</strong>
					{if $chapter->getLocalizedSubtitle() != '' }<br />{$chapter->getLocalizedSubtitle()}{/if}
					{assign var=chapterAuthors value=$chapter->getAuthorNamesAsString()}
					<div class="authorName">{$chapterAuthors}</div>
				{/foreach}
			</div>
			
		{/if}-->
		
		<!-- download files  -->
		{if $availableFiles|@count > 1} <!-- display this area only when there is more than one file to download -->
			
			<h3 class="accordionHeader"><a href="#">{translate key="plugins.generic.bookPage.downloads"}</a></h3>
			<div>
				{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
				{assign var=currency value=$currentPress->getSetting('currency')}
				{foreach from=$publicationFormats item=publicationFormat}
					{if $publicationFormat->getIsAvailable() && $publicationFormat->getIsApproved()}
						
						{assign var=templatePath value="../`$pluginPath`/templates/langsciBookFiles.tpl"}
						{include file=$templatePath availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency}
						
				<!--	{include file="catalog/book/bookFiles.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency} -->
					{/if}
				{/foreach}
				
				<!-- view the code on github -->
				<li>
					<div class="publicationFormatName">
						{translate key="plugins.generic.bookPage.latexSource.text"}
						<a href={"https://github.com/langsci/"}{$publishedMonograph->getId()} target="blank" title="{translate key="plugins.generic.bookPage.latexSource.title"}"><i class="fa fa-github"></i>{translate key="plugins.generic.bookPage.latexSource.link"}</a>
					</div>
				</li>
			</div>	
		{/if}
		
		<!-- Statistics -->
		<!-- TODO: add hook and put statistics in own plugin -->
		
	{if $statImageExists} 
			<h3 class="accordionHeader"><a href="#">{translate key="plugins.generic.bookPage.statistics"}</a></h3>
			<div>
				<a href="{$baseUrl}{"/plugins/generic/bookPage/img/"}{$publishedMonograph->getId()}{".png"}">
					<img class="pkp_helpers_container_center" alt="{$publishedMonograph->getLocalizedFullTitle()|escape}" src="{$baseUrl}{"/plugins/generic/bookPage/img/"}{$publishedMonograph->getId()}{".png"}" width="100%" />
				</a>
		</div>	
	{/if} 
	</div>	
		
</div>
