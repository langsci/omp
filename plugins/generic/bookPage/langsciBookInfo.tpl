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
	
	

	<div class="bookAccordion">
	
	
	
		<!-- display first document of filelist  -->
		<!-- TODO: get name of publicationFormat that should be displayed here from the settings -->
		
		{if $availableFiles|@count != 0}
		
			<h3 class="accordionHeader"><a>{translate key="plugins.generic.bookPage.read"}</a></h3>
			<div>
					{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
					{assign var=currency value=$currentPress->getSetting('currency')}
						{foreach from=$publicationFormats item=publicationFormat}
							{if $publicationFormat->getIsAvailable() && $publicationFormat->getLocalizedName()=="Complete book"}
								{include file="catalog/book/bookFiles.tpl" availableFile=$availableFile publicationFormatId=$publicationFormat->getId() publishedMonograph=$publishedMonograph currency=$currency}
							{/if}
						{/foreach}
	
			</div>
		{/if}
	
	
		<!-- open review  -->
		{if $publishedMonograph->getId()=="25"}
			<h3 class="accordionHeader"><a>{translate key="plugins.generic.bookPage.openReview"}</a></h3>
			<div>
				This book is currently under <a title="Open review work at Language Science Press" href="http://test.langsci-press.org/openReview/intro"> open review</a>. 
				See our <a title="How to comment on our PDF files" href="http://test.langsci-press.org/openReview/userGuide">user guide</a> to get acquainted with the commenting software.
				
				<div>
					<a class="openReviewButton" href="https://via.hypothes.is/http://test.langsci-press.org/public/site/pdf/MuellerOpenReview1.pdf"><img class="icon" src="/public/site/img/userGuideHypothesis/comment.png" alt="" /><span>preliminary version part 1</span></a>
				</div>
				
				<div>
					<a class="openReviewButton" href="https://via.hypothes.is/http://test.langsci-press.org/public/site/pdf/MuellerOpenReview2.pdf"><img class="icon" src="/public/site/img/userGuideHypothesis/comment.png" alt="" /><span>preliminary version part 2</span></a>
				</div>
				
			</div>
		{/if}
	
		<!-- about this book  -->
		<h3 class="accordionHeader"><a href="#">About this book</a></h3>
		<div>
			{$publishedMonograph->getLocalizedAbstract()|strip_unsafe_html}
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
			
				<h3 class="accordionHeader"><a href="#">{translate key="plugins.generic.bookPage.contents"}</a></h3>
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
		<!-- TODO: add hook and put statistics in own plugin -->
		<!-- TODO: get images from catalog -->
		{if $availableFiles|@count != 0}
				<h3 class="accordionHeader"><a href="#">{translate key="plugins.generic.bookPage.statistics"}</a></h3>
				<div>
					{assign var=imageUrl value="/plugins/generic/bookPage/"}
					<img class="pkp_helpers_container_center" alt="{$publishedMonograph->getLocalizedFullTitle()|escape}" src="{$base_url}{$imageUrl}{$publishedMonograph->getId()}{".png"}" />
				</div>	
		{/if}
	</div>	
		
</div>
