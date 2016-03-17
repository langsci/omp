{**
 * plugins/importexport/native/templates/results.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * List of operations this plugin can perform
 *}

<link rel="stylesheet" href="http://localhost/omp1111/plugins/importexport/identificationCodes/css/identificationCodes.css" type="text/css" />

<div id="identificationCodesResults">

{if not $errorMessage}

	{if $import}
		<p class="header">{translate key="plugins.importexport.identificationCodes.results.inserted"}</p>
	{else}
		<p class="header">{translate key="plugins.importexport.identificationCodes.results.toBeInserted"}</p>
	{/if}	


	{if $insertedCodes}

		<table class="identificationCodesTable">
			<tr>
				<th>SubId</th>
				<th>Title</th>
				<th>PubFormat</th>
				<th>Name</th>
				<th>Value</th>
				<th>Note</th>
			</tr>
			{foreach from=$insertedCodes item=entry}
			<tr>
				<td>{$entry.subId}</td>
				<td>{$entry.title}</td>		
				<td>{$entry.publicationFormat}</td>	
				<td>{$entry.codeName}</td>
				<td>{$entry.codeValue}</td>
				<td>{$entry.note}</td>	
			</tr>
			{/foreach}
		</table>
	{else}
		<p>{translate key="plugins.importexport.identificationCodes.results.noEntries"}</p>
	{/if}



	{if $import}
		<p class="header">{translate key="plugins.importexport.identificationCodes.results.notInserted"}</p>
	{else}
		<p class="header">{translate key="plugins.importexport.identificationCodes.results.notToBeInserted"}</p>
	{/if}	

	{if $nonInsertedCodes}

		<table class="identificationCodesTable">
			<tr>
				<th>SubId</th>
				<th>PubFormat</th>
				<th>Name</th>
				<th>Value</th>
				<th>Note</th>
			</tr>
			{foreach from=$nonInsertedCodes item=entry}
			<tr>	
				<td>{$entry.subId}</td>	
				<td>{$entry.publicationFormat}</td>	
				<td>{$entry.codeName}</td>	
				<td>{$entry.codeValue}</td>
				<td>{$entry.note}</td>	
			</tr>
			{/foreach}
		</table>
	{else}
		<p>{translate key="plugins.importexport.identificationCodes.results.noEntries"}</p>
	{/if}

{else}

	{$errorMessage}

{/if}
</div>


