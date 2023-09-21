{render partial="shared/layout/content_header" title=$page_title}
{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}
	<div class="card-deck card-deck--4">
		{render partial=article_item from=$finder->getRecords() item=article}
	</div>
	{paginator items_total_label="{t}articles total{/t}"}

{/if}
