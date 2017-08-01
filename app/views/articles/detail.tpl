<article>
	<header>
		<h1>{$article->getTitle()}</h1>
		{if $tags}
			<p>
			<i class="glyphicon glyphicon-tag"></i>
			{foreach $tags as $tag}
				{if !$tag@first}/{/if}
				{a action="articles/index" tag_id=$tag}{$tag}{/a}
			{/foreach}
			</p>
		{/if}
		<p class="muted">{t author=$article->getAuthor()->getName()|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</header>

	{!$article->getBody()|markdown}
</article>

{if $older_article || $newer_article}

	<ul class="pager">
		{if $older_article}
			<li class="previous">
				<a href="{link_to action=detail id=$older_article}" title="{t}older article{/t}">&larr; {$older_article->getTitle()|truncate:24}</a>
			</li>
		{/if}
		{if $newer_article}
			<li class="next">
				<a href="{link_to action=detail id=$newer_article}" title="{t}newer article{/t}">{$newer_article->getTitle()|truncate:24} &rarr;</a>
			</li>
		{/if}
	</ul>

{/if}
