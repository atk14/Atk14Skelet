<nav aria-label="breadcrumb">
<ol class="breadcrumb">
	{foreach $breadcrumbs as $breadcrumb}
		<li class="breadcrumb-item">
			{if $breadcrumb->getUrl() && !$breadcrumb@last}
				<a href="{$breadcrumb->getUrl()}">{$breadcrumb->getTitle()}</a>
			{else}
				{$breadcrumb->getTitle()}
			{/if}
		</li>
	{/foreach}
</ol>
</nav>
