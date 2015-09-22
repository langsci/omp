
{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/annotations/css/annotations.css" type="text/css" />

<div id="annotations">

<p><span class="header">Name:</span> {$title}</p>
<p><span class="header">Url:</span><a href="https://via.hypothes.is/{$url}"> {$url}</a></p>
<p><span class="header">Number of comments:</span> {$noComments}</p>
<p><span class="header">Number of comments that are replies:</span> {$numberOfCommentsWithReferences}</p>
<p><span class="header">Time of first comment:</span> {$timeStart}</p>
<p><span class="header">Time of last comment:</span> {$timeEnd}</p>

<br>
 <table>

  <tr>
    <td><p class="header">Number of comments per user:</p><br></td>
    <td><p class="header">Number of comments per tag:</p><br></td>
  </tr>

  <tr>
    <td width="300px">
		{if not $countUser}
			<p>no users</p>
		{else}
			{foreach from=$countUser key=k item=v}
   				<li>{$v}<span>{$k}</span></li>
			{/foreach}
		{/if}
	</td>

	<td width="300px">
		{if not $countTags}
			<p>no tags</p>
		{else}
			{foreach from=$countTags key=k item=v}
			   <li>{$v}<span>{$k}</span></li>
			{/foreach}
		{/if}
	</td>
  </tr>
</table> 

</div>

{strip}
		{include file="common/footer.tpl"}
{/strip}
