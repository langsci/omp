{**
 * plugins/generic/groupMail/groupMail.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/groupMail/css/groupMail.css" type="text/css" />

<div id="groupMail">

	<form class="pkp_form" method="post" action="getGroupMailResults">

		<table style="width:100%; margin: 40px 0px 40px 0px;">
			<tr style="height: 40px;">
				<th>OR<sup>1</sup></th>
				<th>AND<sup>2</sup></th>
				<th>NOT<sup>3</sup></th>
				<th>User group</th>
			</tr>

			{assign var="count" value=0}
			{foreach from=$userGroups key=userGroupId item=groupName}
				<tr {if $count mod 2} style="background-color: #eee;"{/if}>
					<td style="width:70px;">
						<input id="OR{$userGroupId}" name="OR{$userGroupId}" type="checkbox" {if $postOr.$count}checked{/if}></input>
					</td>
					<td style="width:70px;">
						<input id="AND{$userGroupId}" name="AND{$userGroupId}" type="checkbox" {if $postAnd.$count}checked{/if}></input>
					</td>
					<td style="width:70px;">
						<input id="NOT{$userGroupId}" name="NOT{$userGroupId}" type="checkbox" {if $postNot.$count}checked{/if}></input>
					</td>
					<td>
						<span>{$groupName}</span>
					</td>
				</tr>
			{assign var=count value=$count+1}
			{/foreach}

		</table> 

		<div >
		<p style="line-height: 5px;">1 <span class="emph">Add</span> all users that belong to <span class="emph">any</span> of the listed user groups</p>
		<p style="line-height: 5px;">2 <span class="emph">Add</span> all users that belong to <span class="emph">all</span> of the listed user groups</p>	
		<p style="line-height: 5px;">3 <span class="emph">Remove all</span> users that belong to <span class="emph">any</span> of the listed user groups</p>	
		</div>

		<div style="padding-top: 40px;">
			<input id="getUsernames" name="getUsernames" type="checkbox" {if $getUsernames}checked{/if}><span style="padding-left: 10px;">Show usernames in the results</span></input><br>
			<input id="getEmails" name="getEmails" type="checkbox" {if $getEmails}checked{/if}><span style="padding-left: 10px;">Show email addresses in the results</span></input>
		</div>

		<div style="padding: 40px 0px 0px 0px;">

			<button style="float:right;" id="buttonSaveToFile" name="buttonSaveToFile" type="submit"
						class="submitFormButton button ui-button ui-widget ui-state-default
						ui-corner-all ui-button-text-only" role="button" >
				<span class="ui-button-text">Save results to file</span>
			</button>
			<button style="float:right;margin-right:10px;" id="buttonShowResults" name="buttonShowResults" type="submit"
					class="submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button" >
				<span class="ui-button-text">Show Results</span>
			</button>
			<a style="float:right;padding-right: 10px;" id="cancelFormButton" class="cancelFormButton" href="{$baseUrl}">Cancel</a>

		</div>
	</form>
	
	{if $results===null}

	{else}
	<div style="background-color: #eee; margin-top:30px;padding:0px 20px 20px 20px;">
		{assign var="noUsers" value=$results|@sizeof}
		<h3>{$noUsers} user{if !$noUsers==1}s{/if} found</h3><br>
		
		<table style="width:100%;">
			{assign var="count" value=0}
			{foreach from=$results key=email item=username}
				<tr style="">
					{if $getUsernames}<td>{$username}</td>{/if}
					{if $getEmails}<td>{$email}</td>{/if}			
				</tr>
			{assign var=count value=$count+1}
			{/foreach}
		</table>
	</div>
	{/if}

</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
