{**
 * plugins/generic/registerPage/templates/subscriptionDetails.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
 *
 *
 *}

<script language="JavaScript" type="text/javascript">
{literal}
    function showReviewerQuestions() {
        if (document.getElementById("checkboxReviewer").checked == false){
            document.getElementById("reviewerQuestions").style.display = 'none';
        } else {
            document.getElementById("reviewerQuestions").style.display = 'block';
        }
    }
{/literal}
</script>

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/registerPage/css/subscriptionDetails.css" type="text/css" />

<div id="subscriptionDetails">

<p class="intro">{translate key="plugins.generic.registerPage.intro2"}</p>

{translate|assign:"supporter_text" key="plugins.generic.registerPage.checkboxSupporter"}
{translate|assign:"author_text" key="plugins.generic.registerPage.checkboxAuthor"}
{translate|assign:"volumeEditor_text" key="plugins.generic.registerPage.checkboxVolumeEditor"}

<div class="section">

	<ul class="checkbox_and_radiobutton">
		<li>
			<input id="checkboxSupporter" name="checkboxSupporter" type="checkbox"></input>
			<label>{eval var=$supporter_text}</label>
		</li>
		<li>
			<input id="checkboxReader" name="checkboxReader" type="checkbox"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxReader"}</label>
		</li>
		<li>
			<input id="checkboxAuthor" name="checkboxAuthor" type="checkbox"></input>
			<label>{eval var=$author_text}</label>
		</li>
		<li>
			<input id="checkboxVolumeEditor" name="checkboxVolumeEditor" type="checkbox"></input>
			<label>{eval var=$volumeEditor_text}</label>
		</li>
		<li>
			<input id="checkboxReviewer" name="checkboxReviewer" type="checkbox" onclick="showReviewerQuestions()"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxReviewer"}</label>

			<div id="reviewerQuestions">
				<p>{translate key="plugins.generic.registerPage.primaryArea"}</p>
				<section>
            	<select id="selectArea1" name="selectArea1">
            	  <optgroup>
            	        <option></option>
            	        {foreach item=a from=$areas}
            	            {if $a==$area1}
            	                <option class="inputOption" selected>{$a}</option>
            	            {else}
            	                <option class="inputOption">{$a}</option>
            	            {/if}
            	        {/foreach}
            	    </optgroup>
            	</select>
        		</section>
				<p>{translate key="plugins.generic.registerPage.secondaryArea"}</p>
				<section>
            	<select id="selectArea2" name="selectArea2">
            	  <optgroup>
            	        <option></option>
            	        {foreach item=a from=$areas}
            	            {if $a==$area2}
            	                <option class="inputOption" selected>{$a}</option>
            	            {else}
            	                <option class="inputOption">{$a}</option>
            	            {/if}
            	        {/foreach}
            	    </optgroup>
            	</select>
        		</section>
				<p>{translate key="plugins.generic.registerPage.tertiaryArea"}</p>
				<section>
            	<select id="selectArea3" name="selectArea3">
            	  <optgroup>
            	        <option></option>
            	        {foreach item=a from=$areas}
            	            {if $a==$area3}
            	                <option class="inputOption" selected>{$a}</option>
            	            {else}
            	                <option class="inputOption">{$a}</option>
            	            {/if}
            	        {/foreach}
            	    </optgroup>
            	</select>
        		</section>
				<p id="reviewLanguageIntro">{translate key="plugins.generic.registerPage.reviewLanguage"}</p>
				<ul id="reviewLanguageList">
					<li>
						<input id="checkboxEnglish" name="checkboxEnglish" type="checkbox"></input>
						<label>{translate key="plugins.generic.registerPage.checkboxEnglish"}</label>
					</li>
					<li>
						<input id="checkboxGerman" name="checkboxGerman" type="checkbox"></input>
						<label>{translate key="plugins.generic.registerPage.checkboxGerman"}</label>
					</li>
					<li>
						<input id="checkboxFrench" name="checkboxFrench" type="checkbox"></input>
						<label>{translate key="plugins.generic.registerPage.checkboxFrench"}</label>
					</li>
					<li>
						<input id="checkboxOther" name="checkboxOther" type="checkbox"></input>
						<label>{translate key="plugins.generic.registerPage.checkboxOther"}</label>
						<input id="inputOtherLanguage" name="inputOtherLanguage" value="{$otherLanguage}" type="text">
					</li>
				</ul>
			<br>
        {if $emptyArea1}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredArea1"}</p>{/if}
        {if not $languageOkay}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredLanguage"}</p>{/if}

			</div> {**end div: reviewerQuestions**}			

		</li>
		<li>
			<input id="checkboxProofreader" name="checkboxProofreader" type="checkbox"></input>
			<label> {translate key="plugins.generic.registerPage.checkboxProofreader"}</label>
		</li>
		<li>
			<input id="checkboxTypesetter" name="checkboxTypesetter" type="checkbox"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxTypesetter"}</label>
		</li>
	</ul>

	<p class="intro">{translate key="plugins.generic.registerPage.intro3"}</p>
	<ul class="checkbox_and_radiobutton">
		<li>
			<input id="checkboxNewsletter" name="checkboxNewsletter" type="checkbox"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxNewsletter"}</label>
		</li>
		<li>
			<input id="checkboxML" name="checkboxML" type="checkbox"></input>
			<label>{translate key="plugins.generic.registerPage.checkboxML"}</label>
		</li>
	</ul>
</div>

</div>



