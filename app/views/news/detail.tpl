<article>
	<header>
		<h1 class="page-header">{$news->getTitle()}</h1>
		<p>{t author=$news->getAuthor()->getName()|h date=$news->getPublishedAt() date_human=$news->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</header>

	{!$news->getBody()|markdown}
</article>

{*
 * Navigation
 * 
 * $news_navi contains links (in <li></li>) to newer and older item.
 *}
<ul class="nav nav-list">
	<li class="nav-header">{t}News{/t}</li>
	{!$news_navi}
	<li class="divider"></li>
	<li>{a action=index}{t}News archive{/t}{/a}</li>
</ul>
