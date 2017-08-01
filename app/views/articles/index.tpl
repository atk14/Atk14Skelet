<h1>{!$page_title}</h1>

{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}

	{render partial=article_item from=$finder->getRecords() item=article}
	{paginator}

{/if}
