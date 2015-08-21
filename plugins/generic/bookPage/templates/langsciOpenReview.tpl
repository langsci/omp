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
	
		<!--<div class="publicationFormatName">{$availableFile->getLocalizedName()|escape}</div> -->
		<div class="publicationFormatLink completeBook">
		
			{if $openreviewLink0}
				{assign var=downloadUrl value=$openreviewLink0}
			{elseif $availableFile->getDocumentType()==$smarty.const.DOCUMENT_TYPE_PDF}
				{url|assign:downloadUrl op="view" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{else}
				{url|assign:downloadUrl op="download" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{/if}
			
			<a href="https://via.hypothes.is/{$downloadUrl}" title='{translate key="plugins.generic.bookPage.openReview.title"}'>
				{translate key="plugins.generic.bookPage.openReview"}
			</a>
		</div>
		
		<div class="lsp-image-help">
		<a title="How does open review work at Language Science Press?" href="http://test.langsci-press.org/openReview/intro"> <img src="/public/site/img/icons/help.svg" width="27px"></a> 
		</div>
	
{/foreach}
