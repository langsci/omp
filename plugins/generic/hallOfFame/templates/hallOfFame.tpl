{**
 * page.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
	
 *
 *
 *}
{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<script language="JavaScript" type="text/javascript">

	var tmp = "url(";
	var baseUrl = "{$baseUrl}";
	var part1 = tmp.concat(baseUrl);
	var path_arrowright = part1.concat("/plugins/generic/hallOfFame/img/arrowright.jpg)"); 
	var path_arrowdown  = part1.concat("/plugins/generic/hallOfFame/img/arrowdown.jpg)");

	{literal}
		function showSubmissions(name) {

			if (document.getElementById(name).style.visibility=="visible") {
				document.getElementById(name).style.visibility="collapse";
				document.getElementById(name.concat('_img')).style.backgroundImage = path_arrowright;				
			}
			else {
				
				document.getElementById(name).style.visibility="visible";
				document.getElementById(name.concat('_img')).style.backgroundImage = path_arrowdown;
			}
		}
	{/literal}

</script> 
<script language="javascript" type="text/javascript" src="add.js"></script>

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/hallOfFame/css/hallOfFame.css" type="text/css" />

<script type="text/javascript">
	// Initialize JS handler for catalog header.

	$(function() {ldelim}
		$(".hallOfFameAccordion" ).accordion({ldelim} collapsible: true, autoHeight: false, active: true {rdelim});
	{rdelim});

</script>


<div id="hallOfFame">

<br>
{translate key="plugins.generic.hallOfFame.intro"}<br><br><br>

{$htmlContent}

</div>

{strip}
		{include file="common/footer.tpl"}
{/strip}
