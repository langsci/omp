{**
 * plugins/generic/hallOfFame/hallOfFame.tpl
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * main template for the hall of fame plugin
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/hallOfFame/css/hallOfFame.css" type="text/css" />


<script type="text/javascript">
	// Initialize JS handler for catalog header.

	$(function() {ldelim}
		$(".hallOfFameAccordion" ).accordion({ldelim} collapsible: true, autoHeight: false, active: true {rdelim});
	{rdelim});

   //capture the click on the a tag
	$(function() {ldelim}
   $(".hallOfFameAccordion h3 a.linkPublicProfile").click(function() {ldelim}
      window.location = $(this).attr('href');
      return false;
   {rdelim});
	{rdelim});

</script>


<div id="hallOfFame">

{$htmlContent}

</div>




{strip}
		{include file="common/footer.tpl"}
{/strip}
