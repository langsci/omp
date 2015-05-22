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
</script>



<link rel="stylesheet" href="{$baseUrl}/plugins/generic/bookPage/BookPagePlugin.css" type="text/css" />

<div class="bookInfo">
	<div class="bookInfoHeader">
		<h3>{$publishedMonograph->getLocalizedFullTitle()|strip_unsafe_html}</h3>
		<div class="authorName">{$publishedMonograph->getAuthorString()}</div>
	</div>
	
	<!-- TODO: display first document of filelist  -->
	
	{if $availableFiles|@count != 0}
		<p>
			<div>
				{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
				{assign var=currency value=$currentPress->getSetting('currency')}
				
				{include file="catalog/book/bookFiles.tpl" availableFile=$availableFile publicationFormatId="66" publishedMonograph=$publishedMonograph currency=$currency}
					
			<div> 
		</p><br> <br>
	{/if}

	
	<!-- display download file 
	{if $availableFiles|@count != 0}
	<p>
		<div>
			{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
			{assign var=currency value=$currentPress->getSetting('currency')}
			{foreach from=$publicationFormats item=publicationFormat}
				{if $publicationFormat->getIsAvailable()}
					{include file="catalog/book/bookFiles.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency}
				{/if}
			{/foreach}
		<div> 
	</p><br> <br>
	{/if}
	 -->

	<!-- about this book  -->
	<div class="bookAccordion">
		<h3 class="langsciBookPage"><a href="#">About this book</a></h3>
		<div>
			{$publishedMonograph->getLocalizedAbstract()|strip_unsafe_html}
		</div>
	
		
		<!-- about the authors / volume editor -->
		{assign var=authors value=$publishedMonograph->getAuthors()}
			{foreach from=$authors item=author}
				{if $author->getIncludeInBrowse()}
				
					<h3 class="langsciBookPage"><a href="#">About {$author->getFullName()}</a></h3>
					<div>
						{assign var=biography value=$author->getLocalizedBiography()|strip_unsafe_html}
						{if $biography != ''}{$biography}{else}{translate key="catalog.noBioInfo"}{/if}
					</div>
				
				{/if}
			{/foreach}
			
		<!-- chapters  
		{if $publishedMonograph->getWorkType() == WORK_TYPE_EDITED_VOLUME && $chapters|@count != 0}
			
				<h3 class="langsciBookPage"><a href="#">Chapters</a></h3>
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
		{if $availableFiles|@count != 0}
			
				<h3 class="langsciBookPage"><a href="#">{translate key="plugins.generic.bookPage.contents"}</a></h3>
				<div>
					{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
					{assign var=currency value=$currentPress->getSetting('currency')}
					{foreach from=$publicationFormats item=publicationFormat}
						{if $publicationFormat->getIsAvailable()}
							{include file="catalog/book/bookFiles.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency}
						{/if}
					{/foreach}
				</div>	
			 
		{/if}
		
		<!-- Statistics -->
		{if $availableFiles|@count != 0}
			
				<h3 class="langsciBookPage"><a href="#">{translate key="plugins.generic.bookPage.statistics"}</a></h3>
				<div>
					{assign var=imageUrl value="/plugins/generic/bookPage/"}
					<a title="{$publishedMonograph->getLocalizedFullTitle()|strip_tags|escape}" href="{$base_url}{$imageUrl}{$publishedMonograph->getId()}{".jpg"}"><img class="pkp_helpers_container_center" alt="{$publishedMonograph->getLocalizedFullTitle()|escape}" src="{$base_url}{$imageUrl}{$publishedMonograph->getId()}{".png"}" /></a>
				</div>	
			
		{/if}
		
	</div>	
		
		
</div>
