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

<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "BreadcrumbList",
	"itemListElement": [
	{foreach $breadcrumbs as $breadcrumb}
	{
		"@type": "ListItem",
		"position": {$breadcrumb@iteration},
		"name": "{$breadcrumb->getTitle()}",
		"item": "{$request->getServerUrl()}{$breadcrumb->getUrl()}"
	}
	{if !$breadcrumb@last},{/if}
	{/foreach}
	]
}
</script>
