{**
 * plugins/generic/hallOfFame/hallOfFame.tpl
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * main template for the hall of fame plugin
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

  
<script>
  $(function() {ldelim}
    $("#tabs").tabs();
  {rdelim});
</script>

<script type="text/javascript">

	{literal}
		function showStarInfo(userGroupId,medal,maxSeries){ 
			if (medal.localeCompare('series')) {
				alert("userGroup: " + userGroupId + ", medal: " + medal );
			} else {
				alert("userGroup: " + userGroupId + ", medal: " + medal+ ", max number of series: " +maxSeries);
			}

		} 
	{/literal}

</script>

<script type="text/javascript">
	// Initialize JS handler for catalog header.

	$(function() {ldelim}
		$(".hallOfFameAccordion").accordion({ldelim} collapsible: true, autoHeight: false, active: true {rdelim});
	{rdelim});

   //capture the click on the a tag
	$(function() {ldelim}
		$(".hallOfFameAccordion h3 a.linkToProfile").click(function() {ldelim}
      		window.location = $(this).attr('href');
      		return false;
		{rdelim});
	{rdelim});

</script>

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/hallOfFame/css/hallOfFame.css" type="text/css" />

<div id="hallOfFame">

<p class="intro">{translate key="plugins.generic.hallOfFame.intro"}</p>

<div id="tabs">
    <ul>
		{foreach from=$userGroups item=userGroup}
        	<li><a href="#tab-{$userGroup.userGroupId}">{$userGroup.userGroupName}</a></li>
		{/foreach}
		{if $settingMedalCount>0 or $settingMedalCount==''}
			<li><a href="#tab-medalCount">Medal count</a></li>
		{/if}
    </ul>

	{foreach from=$userGroups item=userGroup}

		<div id="tab-{$userGroup.userGroupId}">

			{counter start=0 print=false}

			{foreach from=$userGroup.userData item=medalUserData}

				{counter print=false assign=medal}
				{if $medal==1 and $medalUserData.user|@count>0}
					<h2 class="goldTitle tooltip">
						<span class="headerText">Gold {$userGroup.userGroupName|lower}s</span>
						<span class="tooltip">Gold {$userGroup.userGroupName|lower}s have statistically worked on more books than {math equation="x-y" x=100 y=$percentileRankGold} percent of all {$userGroup.userGroupName|lower}s.</span>
					</h2>
				{/if}
				{if $medal==2 and $medalUserData.user|@count>0}
					<h2 class="silverTitle tooltip">
						<span class="headerText">Silver {$userGroup.userGroupName|lower}s</span>
						<span class="tooltip">Silver {$userGroup.userGroupName|lower}s have statistically worked on more books than {math equation="x-y" x=100 y=$percentileRankSilver} percent of all {$userGroup.userGroupName|lower}s.</span>
					</h2>
				{/if}
				{if $medal==3 and $medalUserData.user|@count>0}
					<h2 class="bronzeTitle tooltip">
						<span class="headerText">Bronze {$userGroup.userGroupName|lower}s</span>
						<span class="tooltip">Bronze {$userGroup.userGroupName|lower}s have worked on at least one book.
					</h2>
				{/if}

	 			<div class='hallOfFameAccordion'>
					{foreach from=$medalUserData.user item=user}			
						<h3>
							<div class="headerContent">

								{** user name and (optional) link to profile  **}
								<div class="user" style="width:{math equation="x*y+z" x=$maxNameLength y=12 z=70}px">
									<a class="userName">{$user.fullName}</a>
									{if $user.linkToProfile}<a class="linkToProfile" href="{$user.linkToProfile}">(view profile)</a>{/if}
								</div>

								{** stars **}
								<div class="achievements">
									{if $user.maxSeriesUser}
										<div class="star tooltip">
											<img src='{$baseUrl}/{$imageDirectory}/series.png'>
											<span class="tooltip">Most versatile: {$user.fullName} has worked for {$userGroup.maxSeries} different series as {$userGroup.userGroupName|lower}.</span>
										</div>							
									{/if}
									{if $user.recentMaxAchievementUser}
										<div class="star tooltip">
											<img src='{$baseUrl}/{$imageDirectory}/recent.png'>
											<span class="tooltip">Most active current {$userGroup.userGroupName|lower}: In the last {$settingRecency} months, {$user.fullName} has worked on {$userGroup.maxRecentAchievements} book{if $userGroup.maxRecentAchievements>1}s{/if} as {$userGroup.userGroupName|lower}.</span>
										</div>
									{/if}
									{if !$user.maxSeriesUser && !$user.recentMaxAchievementUser}
										<img src='{$baseUrl}/{$imageDirectory}/empty.png'>
									{/if}
								</div>

								{** achievement bar **}
								{assign var="barWidth" value=$user.numberOfSubmissions*300/$userGroup.maxAchievements}
								<div class="colorBarWrapper tooltip" title="">
									<div class="colorBar" style="width:{$barWidth}px;">
										<span>
											{if $barWidth>=33}{$user.numberOfSubmissions}/{$user.rankPercentile}{else}{/if}
										</span>									
									</div>
									{if $barWidth<33}
										<span>
											{$user.numberOfSubmissions}/{$user.rankPercentile}
										</span>
									{/if}
									<span class="tooltip">{$user.fullName} has worked on {$user.numberOfSubmissions} book{if $user.numberOfSubmissions>1}s{/if} and is thus statistically more active than {$user.rankPercentile}% of the {$userGroup.userGroupName|lower}s.</span>
								</div>
							</div>
						</h3>
			 			<div class="accordionContent">
							<ol>					
								{foreach from=$user.submissions item=submission}
									<li>
										{$submission.name}
										<a class="linkToBookPage" href="{$submission.path}">&rarr;</a>
									</li>
								{/foreach}
							</ol>
						</div>
					{/foreach}
				</div>
			{/foreach}
		</div>

	{/foreach}

	{if $settingMedalCount>0 or $settingMedalCount==''} 
		<div id="tab-medalCount">

			{if $settingMedalCount>0}
				<h2>Top {$settingMedalCount}</h2>
			{/if}
			<ul>
			{foreach from=$medalCount item=user}
				<li>
					<div class="rank">{$user.rank}.</div> 
					<div class="medalCount" style="width:{math equation="x*y" x=$maxPrizes y=35}px;">
						{foreach from=$user.type item=achievementType key=medal}
							{foreach from=$achievementType key=k item=i}
								<div class="tooltip" style="display:inline">
									<img src='{$baseUrl}/{$imageDirectory}/{$medal}.png'>
									<span class="tooltipsmall tooltip">{if $medal=="gold"}Gold {$userGroupNames.$k}{elseif $medal=="silver"}Silver {$userGroupNames.$k}{elseif $medal=="bronze"}Bronze {$userGroupNames.$k}{elseif $medal=="recent"}Most active {$userGroupNames.$k|lower} at present{elseif $medal=="series"}Most versatile {$userGroupNames.$k|lower}{/if}</span>
								</div>									
							{/foreach}											
						{/foreach}
					</div>
					{if $user.linkToProfile}
						<a class="medalCountName" href="{$user.linkToProfile}">{$user.name}</a> 
					{else}
						{$user.name}
					{/if}
				</li>
			{/foreach}
			</ul>
		</div>
	{/if}


</div>

</div>


{strip}
		{include file="common/footer.tpl"}
{/strip}
