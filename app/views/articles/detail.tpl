<article>
	<header>
		{admin_menu for=$article}
		<h1>{$article->getTitle()}</h1>
		{if $tags}
			<p>
			{!"tag"|icon}
			{foreach $tags as $tag}
				{if !$tag@first}/{/if}
				{a action="articles/index" tag_id=$tag _class="badge badge-primary"}{$tag}{/a}
			{/foreach}
			</p>
		{/if}
		<p class="muted">{t author=$article->getAuthor()->getName()|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</header>
	<section class="article-body">
		{!$article->getBody()|markdown}
	</section>
</article>

{if $older_article || $newer_article}

	<ul class="list-inline pager d-flex justify-content-between">
		<li class="list-inline-item previous">
			{if $older_article}
				<a class="btn btn-outline-primary" href="{link_to action=detail id=$older_article}" title="{t}older article{/t}">{!"arrow-left"|icon} {$older_article->getTitle()|truncate:24}</a>
			{/if}
		</li>
		<li class="list-inline-item next">
			{if $newer_article}
				<a class="btn btn-outline-primary" href="{link_to action=detail id=$newer_article}" title="{t}newer article{/t}">{$newer_article->getTitle()|truncate:24} {!"arrow-right"|icon}</a>
			{/if}
		</li>
	</ul>

{/if}
