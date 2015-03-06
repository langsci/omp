{**
 * plugins/generic/topnavi/localnavModified.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 *
 * Modifications:
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Press-Specific Navigation Bar
 *}

{capture assign="publicMenu1"}
	{if $currentPress}
		{if $enableAnnouncements}
			<li><a href="{url router=$smarty.const.ROUTE_PAGE page="announcement"}">{translate key="announcement.announcements"}</a></li>
		{/if}

		{* Carola Fanselow: Catalog rename to Books for Language Science Press*}
		<li><a href="{url router=$smarty.const.ROUTE_PAGE page="catalog"}">Books</a>

		{* Carola Fanselow: Link to series overview added for Language Science Press*}
		<li><a href="{url router=$smarty.const.ROUTE_PAGE page="series"}">Series</a>

		{* Carola Fanselow: About removed
		<li><a href="#">{translate key="navigation.about"}</a>
			<ul>
				{if not empty($contextInfo.contact)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="contact"}">{translate key="about.contact"}</a></li>
				{/if}
				{if not empty($contextInfo.description)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="description"}">{translate key="about.description"}</a></li>
				{/if}
				{if not empty($contextInfo.editorialTeam)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="editorialTeam"}">{translate key="about.editorialTeam"}</a></li>
				{/if}
				{if not empty($contextInfo.editorialPolicies)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="editorialPolicies"}">{translate key="about.policies"}</a></li>
				{/if}	
				<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="submissions"}">{translate key="about.submissions"}</a></li>
				{if not empty($contextInfo.sponsorship)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="sponsorship"}">{translate key="about.pressSponsorship"}</a></li>
				{/if}
			</ul>
		</li>
		*}
	{/if}
{/capture}


{capture assign="publicMenu2"}
	{if $currentPress}

		{* Carola Fanselow: Link to blog added for Language Science Press*}
		<li><a href="{url router=$smarty.const.ROUTE_PAGE page="blog"}">Blog</a>

	{/if}
{/capture}

{capture assign="notPMMenu"}
	{if $currentPress}
			{* Carola Fanselow: Information menu added for Language Science Press *}
			<li>
				<a>Information</a>
				<ul>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="information" op="forAuthors" }">For authors</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="information" op="forEditors" }">For editors</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="information" op="forReaders" }">For readers</a></li>
					{** <li><a href="{url router=$smarty.const.ROUTE_PAGE page="information" op="newsletter" }">Newsletter</a></li> **}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="information" op="templates"  }">Templates</a></li>
				</ul>
			</li>

			{* Carola Fanselow: About menu added for Language Science Press *}
			<li>
				<a>About</a>
				<ul>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="advisoryBoard" }">Advisory Board</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="supporters" }">Supporters</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="hallOfFame" }">Hall of fame</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="contact" }">Contact</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="sponsorship"  }">Sponsorship</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="imprint"  }">Imprint</a></li>
				</ul>
			</li>

	{/if}
{/capture}




<div class="pkp_structure_head_localNav">
	{if !$isUserLoggedIn}

		<ul class="sf-menu">

			{$publicMenu1}
			{$notPMMenu}
			{$publicMenu2}

			{* Carola Fanselow: Home link removed for Language Science Press
			<li><a href="{url router=$smarty.const.ROUTE_PAGE page="index"}">{translate key="navigation.home"}</a></li>
			*}


		</ul>


	{else}{* $isUserLoggedIn *}


		<ul class="sf-menu">


			{$publicMenu1}

{*
			{if !array_intersect(array(ROLE_ID_MANAGER,ROLE_ID_SUB_EDITOR), $userRoles)}
					{$notPMMenu}
			{/if}
*}
{$notPMMenu}
			{$publicMenu2}


			{if $currentPress}

				{* Carola Fanselow: Link to catalog removed for Language Science Press
				<li><a href="{url router=$smarty.const.ROUTE_PAGE page="catalog"}">{translate key="navigation.catalog"}</a></li>
				*}

				{if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR), $userRoles)}
					<li>
						<a href="#">Manage</a>
						<ul>
							<li>
								<a href="{url router=$smarty.const.ROUTE_PAGE page="manageCatalog"}">{translate key="navigation.catalog"}</a>
							</li>
							{if array_intersect(array(ROLE_ID_MANAGER), $userRoles)}
							<li>
								<a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="index"}">{translate key="navigation.settings"}</a>
								<ul>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="press"}">{translate key="context.context"}</a></li>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="website"}">{translate key="manager.website"}</a></li>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="publication"}">{translate key="manager.workflow"}</a></li>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="distribution"}">{translate key="manager.distribution"}</a></li>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="settings" path="access"}">{translate key="navigation.access"}</a></li>
								</ul>
							</li>
							<li>
								<a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="tools" path="index"}">{translate key="navigation.tools"}</a>
								<ul>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="manager" op="importexport"}">{translate key="navigation.tools.importExport"}</a></li>
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="tools" path="statistics"}">{translate key="navigation.tools.statistics"}</a></li>
								</ul>
							</li>

							{* Carola Fanselow: Link to group mail added for Language Science Press *}
							<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="groupMail"}">Group Mail</a></li>

							{/if}
						</ul>
					</li>
				{/if}{* ROLE_ID_MANAGER || ROLE_ID_SUB_EDITOR *}
			{/if}
			{if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_REVIEWER, ROLE_ID_AUTHOR), $userRoles)}
				<li><a href="{url router=$smarty.const.ROUTE_PAGE page="dashboard"}">{translate key="navigation.dashboard"}</a></li>
			{/if}

		</ul>


	{/if}{* $isUserLoggedIn *}
</div>
