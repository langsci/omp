{**
 * templates/catalog/book/bookFiles.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
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
	<li>
		<div class="publicationFormatName">{$availableFile->getLocalizedName()|escape}</div>
		<div class="publicationFormatLink">
			{if $availableFile->getDocumentType()==$smarty.const.DOCUMENT_TYPE_PDF}
				{url|assign:downloadUrl op="view" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{else}
				{url|assign:downloadUrl op="download" path=$publishedMonograph->getId()|to_array:$publicationFormatId:$availableFile->getFileIdAndRevision()}
			{/if}
			
			<a href="{$downloadUrl}" onclick="addPixel({$publishedMonograph->getId()},'{$availableFile->getLocalizedName()}');"><span title="{$availableFile->getDocumentType()|upper|escape}" class="sprite {$availableFile->getDocumentType()|escape}"></span>{if $availableFile->getDirectSalesPrice()}{translate key="payment.directSales.purchase amount=$availableFile->getDirectSalesPrice() currency=$currency}{else}{translate key="payment.directSales.download"}<span title="{translate key="monograph.accessLogoOpen.altText"}" class="sprite openaccess"></span>{/if}</a>
			<div id="vgwpixel"></div> 
		</div>
	</li>
{/foreach}



