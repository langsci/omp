{**
 * plugins/generic/bookPage/langsciOpenReview.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Svantje Lilienthal
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Displays a link to the open review version (hypothesis) of a book file in the public catalog.
 *}

{foreach from=$availableFiles[$publicationFormatId] item=availableFile}{* There will be at most one of these *}

		{if $openreviewLink0}
			{assign var=downloadUrl value=$openreviewLink0}
				
			<div class="publicationFormatLink completeBook">
				<a href="https://via.hypothes.is/{$openreviewLink0}" title='{translate key="plugins.generic.bookPage.openReview.part0.title"}'>
					{translate key="plugins.generic.bookPage.openReview.part0"}
				</a>
			</div>
				
		{elseif $availableFile->getDocumentType()==$smarty.const.DOCUMENT_TYPE_PDF}
			{url|assign:downloadUrl op="view" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{else}
				{url|assign:downloadUrl op="download" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
	
			<div class="publicationFormatLink completeBook">
				<a href="https://via.hypothes.is/{$downloadUrl}" title='{translate key="plugins.generic.bookPage.openReview.title"}'>
					{translate key="plugins.generic.bookPage.openReview"}
				</a>
			</div>
		{/if}
		
		{if $openreviewLink1}
			
			<div class="publicationFormatLink completeBook">
				<a href="https://via.hypothes.is/{$openreviewLink1}" title='{translate key="plugins.generic.bookPage.openReview.part1.title"}'>
					{translate key="plugins.generic.bookPage.openReview.part1"}
				</a>
			</div>
		
		{/if}
		
		{** Carola Fanselow:  nur Anzeigen, wenn ein Link da ist -> nur temporär!! **}		
		{if $openreviewLink0 or $openreviewLink1}
		<div class="lsp-image-help">
		<a title="About open review at Language Science Press" href="/openReview/intro"> <img src="/public/site/img/icons/info.png" width="27px"></a> 
		</div>
		{/if}
	
{/foreach}
