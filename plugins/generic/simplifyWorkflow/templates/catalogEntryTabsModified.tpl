{**
 * controllers/modals/submissionMetadata/form/catalogEntryTabs.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display a submission's catalog entry form.
 *
 *}

<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#newCatalogEntryTabs').pkpHandler(
				'$.pkp.controllers.tab.catalogEntry.CatalogEntryTabHandler',
				{ldelim}
					{if $selectedTab}selected:{$selectedTab},{/if}
					{if $selectedFormatId}selectedFormatId:{$selectedFormatId},{/if}
					{if $tabsUrl}tabsUrl:'{$tabsUrl}',{/if}
					{if $tabContentUrl}tabContentUrl:'{$tabContentUrl}',{/if}
					emptyLastTab: true
				{rdelim});
	{rdelim});
</script>
{if not $hideHelp}<p class="pkp_help">{translate key="catalog.manage.entryDescription"}</p>{/if}

modified!!!!!!!!!!!


<div id="newCatalogEntryTabs">
	<ul>
		<li>
			<a title="submission" href="{url router=$smarty.const.ROUTE_COMPONENT component="tab.catalogEntry.CatalogEntryTabHandler" tab="submission" op="submissionMetadata" submissionId=$submissionId stageId=$stageId tabPos="0"}">{translate key="submission.catalogEntry.monographMetadata"}</a>
		</li>
		<li>
			<a title="catalog" href="{url router=$smarty.const.ROUTE_COMPONENT component="tab.catalogEntry.CatalogEntryTabHandler" tab="catalog" op="catalogMetadata" submissionId=$submissionId stageId=$stageId tabPos="1"}">{translate key="submission.catalogEntry.catalogMetadata"}dfg</a>
		</li>
		{counter start=2 assign="counter"}
		{foreach from=$publicationFormats item=format}
			<li>
				<a id="publication{$format->getId()|escape}"
					href="{url router=$smarty.const.ROUTE_COMPONENT component="tab.catalogEntry.CatalogEntryTabHandler"
					tab="publication"|concat:$format->getId()
					op="publicationMetadata"
					publicationFormatId=$format->getId()
					submissionId=$submissionId
					stageId=$stageId
					tabPos=$counter}">{$format->getLocalizedName()|escape}</a>
			</li>
			{counter} {* increment our counter, assign to $counter variable *}
		{/foreach}
</ul>
