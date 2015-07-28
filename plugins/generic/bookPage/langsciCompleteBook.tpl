{**
 * plugins/generic/bookPage/langsciCompleteBook.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Svantje Lilienthal
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Displays a book file list with download/payment links in the public catalog.
 *}

<script type="text/javascript">

	function addPixel(publishedMonographId, publicationFormatName){ldelim}
	
		if(publicationFormatName=='Complete book') {ldelim}
			document.getElementById("vgwpixel").innerHTML="<img src='{$imageUrl}' alt=''></img>";
		{rdelim}
	
	{rdelim}
	
</script>

{foreach from=$availableFiles[$publicationFormatId] item=availableFile}{* There will be at most one of these *}
	
		<!--<div class="publicationFormatName">{$availableFile->getLocalizedName()|escape}</div> -->
		<div class="publicationFormatLink completeBook">
			{if $availableFile->getDocumentType()==$smarty.const.DOCUMENT_TYPE_PDF}
				{url|assign:downloadUrl op="view" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{else}
				{url|assign:downloadUrl op="download" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{/if}
			<a href="{$downloadUrl}" title="{translate key="plugins.generic.bookPage.downloadPDF.title"}" onclick="addPixel({$publishedMonograph->getId()},'{$availableFile->getLocalizedName()}');">
				
				<!--<span title="{translate key="monograph.accessLogoOpen.altText"}" class="sprite openaccess">-->
				
				<!-- pdf icon --> 
				<!--<span title="{$availableFile->getDocumentType()|upper|escape}" class="sprite {$availableFile->getDocumentType()|escape}"></span>-->
				
				<!-- open access icon --> 
				{if $availableFile->getDirectSalesPrice()}{translate key="payment.directSales.purchase amount=$availableFile->getDirectSalesPrice() currency=$currency}
				{else}
					{translate key="plugins.generic.bookPage.downloadPDF"}
					
					</span>
				{/if}
			</a>
			<div id="vgwpixel"></div> 
		</div>
	
{/foreach}

		<div class="publicationFormatLink printOnDemand">
			<a href="{$hardcoverLink}">
				{translate key="plugins.generic.bookPage.hardcover"}
			</a>
		</div>
		
		<div class="publicationFormatLink printOnDemand">
			<a href="{$softcoverLink}">
				{translate key="plugins.generic.bookPage.softcover"}
			</a>
		</div>



