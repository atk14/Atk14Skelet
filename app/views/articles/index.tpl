{render partial="shared/layout/content_header" title=$page_title}
{if $finder->isEmpty()}

	<p>{t}At the moment there are no news.{/t}</p>

{else}
	<div class="xcard-deck xcard-deck--4 row row-cols-md-4 gx-5">
		{render partial=article_item from=$finder->getRecords() item=article}
	</div>
	{paginator}

{/if}
