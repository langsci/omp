{**
 * plugins/generic/registerPage/templates/sign.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
 *
 *}

{strip}
    {if !$contentOnly}
        {include file="common/header.tpl"}
    {/if}
{/strip}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/registerPage/css/registerPage.css" type="text/css" />

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/registerPage/css/sign.css" type="text/css" />


<div id="registerPage">
<div id="sign">

<form class="pkp_form" method="post" action="validation">


<p class="intro">{translate key="plugins.generic.registerPage.intro1"}
	<a class="privacyStatement" href="#privacyStatement">
            {translate key="plugins.generic.registerPage.privacyStatement"}</a>
</p>

<div id="registerUserInfos">

<div class="sign_inputs">
<div class="inline">
	<input	type="text" maxlength="40" id="inputAcademicTitle" name="inputAcademicTitle" value="{$academicTitle}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.academicTitle"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="text" maxlength="40" name="inputFirstName" id="inputFirstName" value="{$firstName}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.firstName"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="text" maxlength="40" name="inputLastName" id="inputLastName" value="{$lastName}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.lastName"}<span class="req">*</span></label></span>
</div>
</div>

<div class="sign_inputs">
<div class="inline">
	<input	type="text" maxlength="40" " name="inputUsername" id="inputUsername" value="{$username}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.username"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="password" maxlength="40" name="inputPassword" id="inputPassword" value="{$password}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.password1"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="password" maxlength="40" name="inputPassword2" id="inputPassword2" value="{$password2}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.password2"}<span class="req">*</span></label></span>
</div>
</div>


<div class="sign_inputs">
<div class="inline">
	<input	type="text" maxlength="60" name="inputAffiliation" id="inputAffiliation" value="{$affiliation}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.affiliation"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="text" maxlength="60" name="inputEmail" id="inputEmail" value="{$email}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.emailAddress"}<span class="req">*</span></label></span>
</div>
<div class="inline">
	<input	type="text" maxlength="60" name="inputUserUrl" id="inputUserUrl" value="{$userUrl}"/>
	<span><label class="sub_label">{translate key="plugins.generic.registerPage.label.personalWebsiteURL"}</label></span>
</div>
</div>


    <div id="selectCountryDiv">
        <section>
            <select id="selectCountry" name="selectCountry">
              <optgroup>
                    <option></option>
                    {foreach item=c from=$countries }
                        {if $c==$country}
                            <option class="inputOption" selected>{$c}</option>
                        {else}
                            <option class="inputOption">{$c}</option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </section>
		<label class="labelSelect">{translate key="plugins.generic.registerPage.label.country"}</label>
    </div>


    {if $emptyAcademicTitle}<p>{translate key="plugins.generic.registerPage.requiredAcademicTitle"}</p>{/if}
    {if $emptyFirstName}<p>{translate key="plugins.generic.registerPage.requiredFirstName"}</p>{/if}
    {if $emptyLastName}<p>{translate key="plugins.generic.registerPage.requiredLastName"}</p>{/if}
    {if $emptyUsername}<p>{translate key="plugins.generic.registerPage.requiredUsername"}</p>{/if}
    {if not $usernameUnique}<p>{translate key="plugins.generic.registerPage.requiredUsernameUnique"}</p>{/if}
    {if $emptyPassword}<p>{translate key="plugins.generic.registerPage.requiredPassword"}</p>{/if}
    {if $emptyPassword2}<p>{translate key="plugins.generic.registerPage.requiredPassword2"}</p>{/if}
    {if $emptyAffiliation}<p>{translate key="plugins.generic.registerPage.requiredAffiliation"}</p>{/if}
    {if $emptyEmail}<p>{translate key="plugins.generic.registerPage.requiredEmailAddress"}</p>{/if}
    {if not $emailUnique}<p>{translate key="plugins.generic.registerPage.requiredEmailUnique"}</p>{/if}

    {if not $emailValid && not $emptyEmail}<p>{translate key="plugins.generic.registerPage.requiredEmailValid"}</p>{/if}
    {if not $urlOkay}<p>{translate key="plugins.generic.registerPage.requiredUrlValid"}</p>{/if}
    {if not $passwordsMatch && not $emptyPassword && not $emptyPassword2}
        <p>{translate key="plugins.generic.registerPage.requiredPasswordsMatch"}</p>{/if}
    {if not $usernameAlphanumeric && not $emptyUsername}
        <p>{translate key="plugins.generic.registerPage.requiredUsernameAlphanumeric"}</p>{/if}
    {if not $password6Characters && $passwordsMatch && not $emptyPassword && not $emptyPassword2}
        <p>{translate key="plugins.generic.registerPage.requiredPassword6Characters"}</p>{/if}

    <div class="footnotes">
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote0"}</p>
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote1"}</p>
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote2"}</p>
    </div>
</div>


    {include
        file="../plugins/generic/registerPage/templates/subscriptionDetails.tpl"
    }

    {if !$captchaCorrect}

        <p class="intro">{translate key="plugins.generic.registerPage.intro4"}</p>
		<div id="captchSection">
        	<p>{translate key="plugins.generic.registerPage.captcha"}</p>
        	{if $displayCaptchaRequired}
           		<p id="requiredCaptcha">{translate key="plugins.generic.registerPage.captchaError"}</p>
        	{/if}

			<div>
				<input type="text" maxlength="40" name="inputCaptcha" id="inputCaptcha""/>
				<span>
					<label class="sub_label">{$captchaQuestion}<span class="req">*</span></label>
				</span>
			</div>
		</div>

    {else}
        <input class="checkbox" type="checkbox" style="display:none" id="checkboxCaptchaCorrect" name="checkboxCaptchaCorrect" checked="true">
    {/if}


    <input type="hidden" name="captchaQuestion" value="{$captchaQuestion}">
    <input type="hidden" name="captchaSolution" value="{$captchaSolution}">

    <p class="intro">{translate key="plugins.generic.registerPage.intro5"}</p>
	<ul class="checkbox_and_radiobutton">
		<li>
			<input id="checkboxConfirmation" name="checkboxConfirmation" type="checkbox"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxConfirmation"}</label>
		</li>
	</ul>
	<br><br>

	<div class="section formButtons">

		<div>
			<a id="cancelFormButton" class="cancelFormButton" href="{$pressPath}">Cancel</a>
		</div>

		<div>
			<button id="buttonRegister" name="buttonRegister" type="submit"
					class="submitFormButton button ui-button ui-widget ui-state-default
					ui-corner-all ui-button-text-only" role="button" >
					<span class="ui-button-text">Register</span>
			</button>
		</div>

	</div>

</form> {** action="check" *}

<div id="privacyStatement">
	<p class="intro">{translate key="plugins.generic.registerPage.privacyStatement"}</p>
	<p>{translate key="plugins.generic.registerPage.contentPrivacyStatement"}</p>
</div>

</div>
</div>

<script language="JavaScript" type="text/javascript">

   var issetCheckboxSupporter = "{$issetCheckboxSupporter}";
   var issetCheckboxReader = "{$issetCheckboxReader}";
   var issetCheckboxAuthor = "{$issetCheckboxAuthor}";
   var issetCheckboxVolumeEditor = "{$issetCheckboxVolumeEditor}";
   var issetCheckboxReviewer = "{$issetCheckboxReviewer}";
   var issetCheckboxProofreader = "{$issetCheckboxProofreader}";
   var issetCheckboxTypesetter = "{$issetCheckboxTypesetter}";
   var issetCheckboxNewsletter = "{$issetCheckboxNewsletter}";
   var issetCheckboxML = "{$issetCheckboxML}";
   var issetCheckboxConfirmation = "{$issetCheckboxConfirmation}";
   var issetCheckboxEnglish = "{$issetCheckboxEnglish}";
   var issetCheckboxGerman = "{$issetCheckboxGerman}";
   var issetCheckboxFrench = "{$issetCheckboxFrench}";
   var issetCheckboxOther = "{$issetCheckboxOther}";

    {literal}
        document.getElementById("checkboxSupporter").checked = issetCheckboxSupporter;
        document.getElementById("checkboxReader").checked = issetCheckboxReader;
        document.getElementById("checkboxAuthor").checked = issetCheckboxAuthor;
        document.getElementById("checkboxVolumeEditor").checked = issetCheckboxVolumeEditor;
        document.getElementById("checkboxConfirmation").checked = issetCheckboxConfirmation;
        document.getElementById("checkboxReviewer").checked = issetCheckboxReviewer;
        document.getElementById("checkboxProofreader").checked = issetCheckboxProofreader;
        document.getElementById("checkboxTypesetter").checked = issetCheckboxTypesetter;
        document.getElementById("checkboxNewsletter").checked = issetCheckboxNewsletter;
        document.getElementById("checkboxML").checked = issetCheckboxML;
        document.getElementById("checkboxEnglish").checked = issetCheckboxEnglish;
        document.getElementById("checkboxGerman").checked = issetCheckboxGerman;
        document.getElementById("checkboxFrench").checked = issetCheckboxFrench;
        document.getElementById("checkboxOther").checked = issetCheckboxOther;

        if (issetCheckboxReviewer) {
            document.getElementById("reviewerQuestions").style.display = 'block';
        }
    {/literal}

</script>


{strip}
        {include file="common/footer.tpl"}
{/strip}
