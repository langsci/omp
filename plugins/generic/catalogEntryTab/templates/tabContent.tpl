{**
 * catalog/form/catalogMetadataFormFields.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#additionalTabForm').pkpHandler(
			'$.pkp.controllers.catalog.form.CatalogMetadataFormHandler',
			{ldelim}
				trackFormChanges: true,
				$uploader: $('#plupload_catalogMetadata'),
				uploaderOptions: {ldelim}
					uploadUrl: '{url|escape:javascript op="uploadCoverImage" escape=false stageId=$stageId submissionId=$submissionId}',
					baseUrl: '{$baseUrl|escape:javascript}'
				{rdelim}
			{rdelim}
		);
	{rdelim});
</script>

<form class="pkp_form" id="additionalTabForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="saveForm"}">

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="catalogMetadataFormFieldsNotification"}

	<input type="hidden" name="submissionId" value="{$submissionId|escape}" />
	<input type="hidden" name="stageId" value="{$stageId|escape}" />
	<input type="hidden" name="tabPos" value={$tabPos|escape} />
	<input type="hidden" name="displayedInContainer" value="{$formParams.displayedInContainer|escape}" />
	<input type="hidden" name="tab" value={$tab|escape} />

	<div class="pkp_helpers_clear"></div>

	{fbvFormArea id="booklinks"  class="border" title="plugins.generic.catalogEntryTab.coverlinks"}
		{fbvFormSection }
			{fbvElement label="plugins.generic.catalogEntryTab.coverlinks.soft" type="text"  name="softcoverlink" id="softcoverlink" value=$softcoverlink size=$fbvStyles.size.MEDIUM} 
			{fbvElement label="plugins.generic.catalogEntryTab.coverlinks.hard" type="text"  name="hardcoverlink" id="hardcoverlink" value=$hardcoverlink size=$fbvStyles.size.MEDIUM} 
		{/fbvFormSection}
	{/fbvFormArea}
	
	{fbvFormArea id="openreview"  class="border" title="plugins.generic.catalogEntryTab.openReview"}
		{fbvFormSection }
			{fbvElement label="plugins.generic.catalogEntryTab.openReview.link0" type="text"  name="openreviewlink0" id="openreviewlink0" value=$openreviewlink0 size=$fbvStyles.size.MEDIUM}
			{fbvElement label="plugins.generic.catalogEntryTab.openReview.link1" type="text"  name="openreviewlink1" id="openreviewlink1" value=$openreviewlink1 size=$fbvStyles.size.MEDIUM}  
		{/fbvFormSection}
	{/fbvFormArea}
	
	{fbvFormArea id="reviews"  class="border" title="plugins.generic.catalogEntryTab.reviews"}
		{fbvFormSection }
			{fbvElement label="plugins.generic.catalogEntryTab.reviews.description" type="text"  name="reviews" id="reviews" value=$reviews size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	
	{fbvFormButtons id="catalogMetadataFormSubmit" submitText="common.save"}

</form>
