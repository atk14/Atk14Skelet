<article>
	<h2>{a action=detail id=$news}{$news->getTitle()}{/a}</h2>
	<p>{t author=$news->getAuthor()->getName()|h date=$news->getPublishedAt() date_human=$news->getPublishedAt()|format_date escape=no}Posted by <em class="label">%1</em> on <time datetime="%2">%3</time>{/t}</p>
</article>
