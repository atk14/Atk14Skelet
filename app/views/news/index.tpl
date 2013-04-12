<h2>{$page_title}</h2>

<ul>
	{render partial=news_item from=$finder->getRecords() item=news}
</ul>

{paginator}
