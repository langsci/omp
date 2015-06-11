

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#searchLocaleFileForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

{url|assign:actionUrlSearch router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.customLocale.controllers.grid.CustomLocaleGridHandler" op="searchLocaleFile" currentPage=$currentPage locale=$locale key=$filePath  anchor="localeContents" escape=false}

<form class="pkp_form" id="searchLocaleFileForm" method="post" action="{$actionUrlSearch}">

	<input type="text" size="50"></input>

	{fbvFormButtons id="submitCustomLocaleFileTemplate" submitText="plugins.generic.customLocale.save"}

</form>


