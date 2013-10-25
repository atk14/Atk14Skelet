<h1>{$page_title}</h1>

{render partial=news_item from=$finder->getRecords() item=news}

{paginator}
