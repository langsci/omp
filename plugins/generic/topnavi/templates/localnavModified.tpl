{**
 * plugins/generic/topnavi/templates/localnavModified.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}

{capture assign="publicMenu1"}
	{if $currentPress}

		<li><a href="{url router=$smarty.const.ROUTE_PAGE page="catalog"}">{translate key="plugins.generic.topnavi.lable.books"}</a>
		<li><a href="{url router=$smarty.const.ROUTE_PAGE page="series"}">{translate key="plugins.generic.topnavi.lable.series"}</a>

	{/if}
{/capture}


{capture assign="publicMenu2"}
	{if $currentPress}

		<li><a href="http://userblogs.fu-berlin.de/langsci-press/">{translate key="plugins.generic.topnavi.lable.blog"}</a>

	{/if}
{/capture}

{capture assign="notPMMenu"}
	{if $currentPress}

			<li>
				<a>{translate key="plugins.generic.topnavi.lable.guidelines"}</a>
				<ul>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="forAuthors" }">{translate key="plugins.generic.topnavi.lable.forAuthors"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="forEditors" }">{translate key="plugins.generic.topnavi.lable.forEditors"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="forReaders" }">{translate key="plugins.generic.topnavi.lable.forReaders"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="forLibrarians" }">{translate key="plugins.generic.topnavi.lable.forLibrarians"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="templatesAndTools" }">{translate key="plugins.generic.topnavi.lable.templates"}</a></li>
				</ul>
			</li>

			<li>
				<a>{translate key="plugins.generic.topnavi.lable.information"}</a>
				<ul>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" }">{translate key="plugins.generic.topnavi.lable.about"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="support" }">{translate key="plugins.generic.topnavi.lable.supportUs"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="advisoryBoard" }">{translate key="plugins.generic.topnavi.lable.advisoryBoard"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="supporters"}">{translate key="plugins.generic.topnavi.lable.supporters"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="hallOfFame" }">{translate key="plugins.generic.topnavi.lable.hallOfFame"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="contact" }">{translate key="plugins.generic.topnavi.lable.contact"}</a></li>
					{if not empty($contextInfo.editorialPolicies)}
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="editorialPolicies"}">{translate key="plugins.generic.topnavi.lable.editorialPolicies"}</a></li>
					{/if}	
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="sponsorship"  }">{translate key="plugins.generic.topnavi.lable.sponsorship"}</a></li>
					<li><a href="{url router=$smarty.const.ROUTE_PAGE page="about" op="imprint" }">{translate key="plugins.generic.topnavi.lable.imprint"}</a></li>
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

				{if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR), $userRoles)}
					<li>
						<a href="#"><img src="/public/site/img/icons/settings.png" width="20px"/></a>
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
									<li><a href="{url router=$smarty.const.ROUTE_PAGE page="management" op="vgWort" path="index"}">{translate key="plugin.generic.vgWort.navigation"}</a>
							</li>
								</ul>
							</li>

							{/if}
						</ul>
					</li>
				{/if}{* ROLE_ID_MANAGER || ROLE_ID_SUB_EDITOR *}
			{/if}
			{if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_REVIEWER, ROLE_ID_AUTHOR), $userRoles)}
				<li><a href="{url router=$smarty.const.ROUTE_PAGE page="dashboard"}"><img src="/public/site/img/icons/dashboard.png" title="Panel" width="20px"/></a></li>
			{/if}

		</ul>

	{/if}{* $isUserLoggedIn *}
</div>
