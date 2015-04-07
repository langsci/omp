{**
 * plugins/generic/userWebsiteSettings/templates/userWebsiteSettings.tpl
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * main template for the User website settings plugin. 
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/userWebsiteSettings/css/userWebsiteSettings.css" type="text/css" />

<script type="text/javascript">
{literal}
function popup (url) {
 pwind = window.open(url, "Profile Image", "width=480,height=330");
 pwind.focus();
 return false;
{/literal}
}
</script>

<div id="langsciUserWebsiteSettings">

	<p class="intro">{translate key="plugins.generic.userWebsiteSettings.intro"}</p>

	<form class="pkp_form" method="post" action="viewUserWebsiteSettings">

	<div class="section">

		<ul class="checkbox_and_radiobutton">
			{if $PublicProfile}
				<li>
					<input id="checkboxPublicProfile" name="checkboxPublicProfile" type="checkbox"></input>
					<label>
						Display my
						<a href="{$pressPath}/{$pathPublicProfiles}/{$userId}">public profile</a> on the Language Science Press Website
				</label>
				</li>
			{/if}
			{if $Email}
				<li>
					<input id="checkboxEmail" name="checkboxEmail" type="checkbox"></input>
					<label>
						Display my email adress in my public profile
					</label>
				</li>
			{/if}
			{if $HallOfFame}
				<li>
					<input id="checkboxHallOfFame" name="checkboxHallOfFame" type="checkbox"></input>
					<label>
						Include me in the <a href="{$pressPath}/{$pathHallOfFame}">hall of fame</a>
					</label>
				</li>
			{/if}     
    	</ul>
	</div>

	<div class="links">
		{if $ProfileImage}
			<p>View/upload/remove my profile image:
				<a href="uploadProfileImage" target="_blank" onclick="return popup(this.href);">Profile Image</a>
			</p>	
		{/if} 
	</div>

	<div class="section formButtons ">
		<div>
			<a id="cancelFormButton" class="cancelFormButton" href="{$pressPath}">Cancel</a>
		</div>

		<div>
			<button id="buttonSaveWebsiteSettings" name="buttonSaveWebsiteSettings" type="submit"
					class="submitFormButton button ui-button ui-widget
					ui-state-default ui-corner-all ui-button-text-only" role="button" >
					<span class="ui-button-text">Save changes</span>
			</button>
		</div>
	</div>

	</form> 

</div>

<script language="JavaScript" type="text/javascript">
 
	var issetCheckboxPublicProfile = "{$issetCheckboxPublicProfile}";
	var issetCheckboxEmail = "{$issetCheckboxEmail}";
	var issetCheckboxHallOfFame = "{$issetCheckboxHallOfFame}";

	{literal}

		document.getElementById("checkboxPublicProfile").checked = issetCheckboxPublicProfile;
		document.getElementById("checkboxEmail").checked = issetCheckboxEmail;
		document.getElementById("checkboxHallOfFame").checked = issetCheckboxHallOfFame;

	{/literal}

</script>


{strip}
		{include file="common/footer.tpl"}
{/strip}

