<header>
	<h1>{!$page_title}</h1>
</header>
{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}
	<div class="card-deck card-deck-4">
		{render partial=article_item from=$finder->getRecords() item=article}
	</div>
	{paginator}

{/if}
