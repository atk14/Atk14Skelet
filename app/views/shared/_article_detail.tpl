<article>
	<header>
		<h1>{$article->getTitle()}</h1>
		<p class="muted">{t author=$article->getAuthor()->getName()|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</header>

	{!$article->getBody()|markdown}
</article>
