<h1>{$page_title}</h1>

{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}

	{render partial=news_item from=$finder->getRecords() item=news}
	{paginator}

{/if}
