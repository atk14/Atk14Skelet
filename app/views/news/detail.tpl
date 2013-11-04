<article>
	<header>
		<h1>{$news->getTitle()}</h1>
		<p class="muted">{t author=$news->getAuthor()->getName()|h date=$news->getPublishedAt() date_human=$news->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</header>

	{!$news->getBody()|markdown}
</article>

<ul class="pager">
	{if $older_news}
		<li class="previous">
			{a action=detail id=$older_news}&larr; {$older_news->getTitle()|truncate:24}{/a}
		</li>
	{/if}
	<li>{a action=index _class="muted"}<i class="icon-list"></i> {t}News archive{/t}{/a}</li>
	{if $newer_news}
		<li class="next">
			{a action=detail id=$newer_news}{$newer_news->getTitle()|truncate:24} &rarr;{/a}
		</li>
	{/if}
</ul>
