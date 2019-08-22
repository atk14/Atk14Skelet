<article class="media">
	<img {!$article->getImageUrl()|img_attrs:"64x64xcrop"} class="mr-3">
	<div class="media-body">
	<h2>{a action=detail id=$article}{$article->getTitle()}{/a}</h2>
	<p>
		{$article->getTeaser()}
	</p>
	<p>{t author=$article->getAuthor()|user_name|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</div>
</article>
