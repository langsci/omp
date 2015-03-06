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


<link rel="stylesheet" href="{$baseUrl}/plugins/generic/hallOfFame/css/hallOfFame.css" type="text/css" />


<div class="LSP">
<div class="hallOfFame">
<br>
{translate key="plugins.generic.hallOfFame.intro"}


{$htmlContent}

</div> 
</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
