{**
 * controllers/grid/settings/user/userGridFilter.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2000-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Filter template for user grid.
 *}
<script type="text/javascript">
	// Attach the form handler to the form.
	$('#customLocaleSearchForm').pkpHandler('$.pkp.controllers.form.ClientFormHandler',
		{ldelim}
			trackFormChanges: false
		{rdelim}
	);
</script>

<form class="pkp_form" id="customLocaleSearchForm" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler" op="fetchGrid"}" method="post">
	{fbvFormArea id="userSearchFormArea"}
		{fbvFormSection title="common.search" for="search"}
			{fbvElement type="text" name="search" id="search" value=$filterSelectionData.search size=$fbvStyles.size.LARGE inline="true"}
			{fbvElement type="select" name="locale" id="locale" from=$filterData.localeOptions selected=$filterSelectionData.locale size=$fbvStyles.size.SMALL translate=false inline="true"}
		{/fbvFormSection}

		{fbvFormButtons hideCancel=true submitText="common.search"}
	{/fbvFormArea}
</form>

<div class="pkp_helpers_clear">&nbsp;</div>




