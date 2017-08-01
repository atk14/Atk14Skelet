<article>
	<h2>{a action=detail id=$article}{$article->getTitle()}{/a}</h2>
	<p>{t author=$article->getAuthor()->getName()|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
</article>
