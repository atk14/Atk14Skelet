<article>
	<h1 class="page-header">{$news->getTitle()}</h1>
	{assign var=author value=$news->getAuthor()}
	{if $author}
		<p class="author">{t author=$author->getName()|h date=$news->getPublishedAt() date_human=$news->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	{/if}

	{!$news->getBody()|markdown}
</header>

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
