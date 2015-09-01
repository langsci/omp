{**
 * templates/catalog/book/bookSpecs.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display the book specs portion of the public-facing book view.
 *}

<script type="text/javascript">
	// Initialize JS handler for catalog header.
//	$(function() {ldelim}
//		$('.bookAccordion').accordion({ldelim} autoHeight: false {rdelim});
//	{rdelim});
	
	$(function() {ldelim}
		$(".bookAccordion" ).accordion({ldelim} collapsible: true, autoHeight: false, active: true {rdelim});
	{rdelim});
	
</script>

{** Carola Fanselow: the first download link ($downloadUrl) is used for the cover image 
{if $availableFiles|@count != 0}
	{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats()}
	{if $publicationFormats|@count>0}
	{if $publicationFormats[0]->getIsAvailable()}
		{assign var="publicationFormatId" value=$publicationFormats[0]->getId()}
		{assign var="availableFilesPF" value=$availableFiles[$publicationFormatId]}
		{assign var="availableFile" value=$availableFilesPF[0]}
		{if $availableFile->getDocumentType()==$smarty.const.DOCUMENT_TYPE_PDF}
			{url|assign:downloadUrl op="view" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
		{else}
			{url|assign:downloadUrl op="download" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
		{/if}
	{/if}
	{/if}
{/if}

 end carola **}

<div class="bookSpecs">
	{assign var=coverImage value=$publishedMonograph->getCoverImage()}
		<a title="{$publishedMonograph->getLocalizedFullTitle()|strip_tags|escape}" href="{$downloadUrl}">
			<img id="coverImageOpenReview" class="pkp_helpers_container_center" alt="{$publishedMonograph->getLocalizedFullTitle()|escape}" src="{url router=$smarty.const.ROUTE_COMPONENT 		component="submission.CoverHandler" op="catalog" submissionId=$publishedMonograph->getId()}">
			</a>
	<div class="bookAccordion">
		<h3><a href="#">{translate key="catalog.publicationInfo"}</a></h3>
		<div class="publicationInfo">
			<div class="dateAdded">{translate key="catalog.dateAdded" dateAdded=$publishedMonograph->getDatePublished()|date_format:$dateFormatShort}</div>
			{assign var=publicationFormats value=$publishedMonograph->getPublicationFormats(true)}
			{if count($publicationFormats) === 1}
				{foreach from=$publicationFormats item="publicationFormat"}
					{if $publicationFormat->getIsApproved()}
						{include file="catalog/book/bookPublicationFormatInfo.tpl" publicationFormat=$publicationFormat availableFiles=$availableFiles}
					{/if}
				{/foreach}
			{/if}
			{if $series}
				<div class="seriesLink">{translate key="series.series"}: <a href="{url page="catalog" op="series" path=$series->getPath()}">{$series->getLocalizedFullTitle()}</a></div>
			{/if}
		
			<br>
			<!-- cite as -->
			<div class="citeAs">
				<!-- get type of book: edited volume or monograph -->
				{assign var=type value=$publishedMonograph->getWorkType()}
				
				<!-- authors / volume editors -->
				{assign var=authors value=$publishedMonograph->getAuthors()}
				{assign var=authorCount value=$authors|@count}
				
				{if $type == "0" || $type == "2"} 
					{foreach from=$authors item=author name=authors key=i}
						{assign var=userGroup value=$author->getUserGroupId()}
						{if $userGroup == 5}
							{assign var=firstName value=$author->getFirstName()}
							{assign var=middleName value=$author->getMiddleName()}
							{assign var=lastName value=$author->getLastName()}
							{if $i == 0}
								{$firstName|escape} {$middleName|escape} {$lastName}
							{elseif $i}
								&amp; {$lastName|escape}, {$middleName|escape} {$firstName}
							{/if}
						{/if}
					{/foreach}
				{elseif $type == "1"}
					{assign var=authorsDisplayed value=0}
					{foreach from=$authors item=author name=authors key=i}
						{assign var=userGroup value=$author->getUserGroupId()}
						{if $userGroup == 6}
							{assign var=firstName value=$author->getFirstName()}
							{assign var=middleName value=$author->getMiddleName()}
							{assign var=lastName value=$author->getLastName()}
							{if $i == 0}
								{$firstName|escape} {$middleName|escape} {$lastName}
							{elseif $i}
								&amp; {$lastName|escape}, {$middleName|escape} {$firstName}
							{/if}
							{assign var=authorsDisplayed value=$authorsDisplayed+1}
						{/if}
					{/foreach}
					{if $authorsDisplayed == 1}(ed.){/if}
					{if $authorsDisplayed > 1}(eds.){/if}
				{/if}
				
				<!-- year -->
				
				{assign var=datePublished value="????"}
				
				{if $publishedMonograph->getDatePublished()}{assign var=datePublished value= $publishedMonograph->getDatePublished()|date_format:'%Y'}{/if}
				
				{$datePublished}
				
				<!-- title -->
				<em>{$publishedMonograph->getLocalizedFullTitle()|strip_tags|escape}</em>
				
				<!-- series -->
				({$series->getLocalizedFullTitle()|escape} 
				
				<!-- series position -->
				{assign var=seriesPosition value=$publishedMonograph->getSeriesPosition()}
				{if $seriesPosition == ""}
					tba).
				{elseif $seriesPosition}
					{$seriesPosition|escape}). 
				{/if}
				
				<!-- press -->
				Berlin: Language Science Press.
				
			</div>
			<!-- end cite as -->
			
		</div>
	</div>

	{if count($publicationFormats) > 1}
		{foreach from=$publicationFormats item="publicationFormat"}
			{if $publicationFormat->getIsApproved()}
				<div class="bookAccordion">
					<h3><a href="#">{$publicationFormat->getLocalizedName()|escape}</a></h3>
					<div class="publicationFormat">
						{include file="catalog/book/bookPublicationFormatInfo.tpl" publicationFormat=$publicationFormat availableFiles=$availableFiles}
					</div>{* publicationFormat *}
				</div>
			{/if}{* $publicationFormat->getIsApproved() *}
		{/foreach}{* $publicationFormats *}
	{/if}{* publicationFormats > 1 *}

	{assign var=categories value=$publishedMonograph->getCategories()}
	{if !$categories->wasEmpty()}
		<div class="bookAccordion">
			<h3><a href="#">{translate key="catalog.relatedCategories}</a></h3>
			<div>
				<ul class="relatedCategories">
					{iterate from=categories item=category}
						<li><a href="{url op="category" path=$category->getPath()}">{$category->getLocalizedTitle()|strip_unsafe_html}</a></li>
					{/iterate}{* categories *}
				</ul>
			</div>
		</div>
	{/if}{* !$categories->wasEmpty() *}
	
</div>
