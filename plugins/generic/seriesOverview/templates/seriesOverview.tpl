{**
 * plugins/generic/seriesOverview/seriesOverview.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/seriesOverview/css/seriesOverview.css" type="text/css" />

<script type="text/javascript">
	// Initialize JS handler for catalog header.

	$(function() {ldelim}
		$(".seriesOverviewAccordion" ).accordion({ldelim} collapsible: true, autoHeight: false, active: true {rdelim});
	{rdelim});

   //capture the click on the a tag
	$(function() {ldelim}
		$(".seriesOverviewAccordion h3 a.linkToSeries").click(function() {ldelim}
      		window.location = $(this).attr('href');
      		return false;
		{rdelim});
	{rdelim});

</script>

{if $useImages}
<style>
  span.ui-icon {ldelim} display:none !important; {rdelim}
	div.header {ldelim} padding:0 !important; {rdelim}
</style>
{/if}

<div id="seriesOverview">
	{foreach from=$series item=seriesGroup key=key}

	{**	{foreach from=$seriesGroup item=singleSeries}
<img  src="{url router=$smarty.const.ROUTE_PAGE page="catalog" width="10" height="10" op="thumbnail" type="series" id=$singleSeries.seriesObject->getId()}">
	{/foreach}**}

	{if $key=="incubation"}
		<p class="sectionHeader">
			{translate key="plugins.generic.seriesOverview.incubationSection"}
		</p>
	{/if}	

	<div class='seriesOverviewAccordion'>
		{foreach from=$seriesGroup item=singleSeries}
			<h3>
				<div class="header">
					{if $useImages}<img  class="listIconImage" src="{url router=$smarty.const.ROUTE_PAGE page="catalog"
					op="thumbnail" type="series" id=$singleSeries.seriesObject->getId()}">{/if}
					{**{if $useImages}<img class="listIconImage"
					src='{url router=$smarty.const.ROUTE_PAGE page="catalog" op="thumbnail"
					type="series" id=$singleSeries.seriesObject->getId()}' alt='-'>{/if}**}
					<div class="headerText">		
						<span class="seriesTitle">{$singleSeries.seriesObject->getLocalizedFullTitle()}</span>
						<span class='numberOfBooks'">({$singleSeries.numberOfPublishedBooks} {if $singleSeries.numberOfPublishedBooks==1}{translate key="plugins.generic.seriesOverview.book"}{else}{translate key="plugins.generic.seriesOverview.books"}{/if}
								{if $singleSeries.numberOfForthcomingBooks>0}, {$singleSeries.numberOfForthcomingBooks} {translate key="plugins.generic.seriesOverview.forthcoming"}{/if})
						</span>			
					</div>
					<a href={$singleSeries.link} class='linkToSeries'>{translate key="plugins.generic.seriesOverview.linkToSeries"}</a>
				</div>
			</h3> 
			<div class='accordionContent'>
				{if $useImages}<img class="contentImage" src='{url router=$smarty.const.ROUTE_PAGE page="catalog"
					op="fullSize" type="series" id=$singleSeries.seriesObject->getId()}' alt='-'>{/if}
				<div class="bookList">
					<ul>
						{if $singleSeries.numberOfBooks>0}
    						{foreach from=$singleSeries.monographs item=publishedMonograph}
								<li class='books'><a href={$publishedMonograph.link}>{$publishedMonograph.fullTitle}</a></li>
	    					{/foreach} 
						{else}
							<li>{translate key="plugins.generic.seriesOverview.noPublications"}</li>
						{/if}
					</ul>	
				</div>
			</div>
 		{/foreach} 
	</div>
 	{/foreach} 
</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
