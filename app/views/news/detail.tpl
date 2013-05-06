<h2>{$news->getPublishedAt()|format_date} {$news->getTitle()}</h2>
{assign var=author value=$news->getAuthor()}
{if $author}
	<p class="author">{t author=$author->getName()|h escape=no}Posted by <em>%1</em>{/t}</p>
{/if}

{!$news->getBody()|markdown} 

<ul class="nav nav-list">
	{foreach from=$news_navi->getItems() item=item}
		{!$item}
	{/foreach}
</ul>
