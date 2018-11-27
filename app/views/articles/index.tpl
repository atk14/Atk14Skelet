<h1>{!$page_title}</h1>

{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}
	<div class="article-items">
		{render partial=article_item from=$finder->getRecords() item=article}
	</div>
	{paginator}

{/if}
