<article>
	{capture assign="article_meta"}{t author=$article->getAuthor()|user_name|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}{/capture}
	{render partial="shared/layout/content_header" title=$article->getTitle() teaser=$article->getTeaser()|markdown tags=$tags meta=$article_meta}

	{admin_menu for=$article}
	<section class="article-body">
		{if !$article->isPublished()}
			<p><em>{t}This is not a published article! It's not available to the public audience.{/t}</em></p>
		{/if}
		{!$article->getBody()|markdown}
	</section>
</article>

{if $older_article || $newer_article}

	<ul class="list-inline pager d-flex justify-content-between">
		<li class="list-inline-item previous">
			{if $newer_article}
				<a class="btn btn-outline-primary" href="{link_to action=detail id=$newer_article}" title="{t}newer article{/t}">{!"arrow-left"|icon} {$newer_article->getTitle()|truncate:24}</a>
			{/if}
		</li>
		<li class="list-inline-item next">
			{if $older_article}
				<a class="btn btn-outline-primary" href="{link_to action=detail id=$older_article}" title="{t}older article{/t}">{$older_article->getTitle()|truncate:24} {!"arrow-right"|icon}</a>
			{/if}
		</li>
	</ul>

{/if}
