{**
 * lib/pkp/templates/user/profile.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * User profile page.
 *}
{include file="common/header.tpl" pageTitle="user.profile.publicProfile"}

<script type="text/javascript">
        // Attach the JS file tab handler.
        $(function() {ldelim}
                $('#userProfileTabs').pkpHandler('$.pkp.controllers.TabHandler');
        {rdelim});
</script>

<p><a href="subscriptions">My Subscriptions and User Groups</a></p>
<p><a href="viewUserWebsiteSettings">My Website Settings</a></p>
<br>

<div id="userProfileTabs">
	<ul>
		<li>
			<a href="{url router=$smarty.const.ROUTE_COMPONENT component="tab.user.ProfileTabHandler" op="profile"}">{translate key="user.profile"}</a>
		</li>
		<li>
			<a href="{url router=$smarty.const.ROUTE_COMPONENT component="tab.user.ProfileTabHandler" op="notificationSettings"}">{translate key="notification.notifications"}</a>
		</li>

	</ul>
</div>

{include file="common/footer.tpl"}
