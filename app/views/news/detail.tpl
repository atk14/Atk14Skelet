<h2>{$news->getPublishedAt()|format_date} {$news->getTitle()}</h2>
{assign var=author value=$news->getAuthor()}
{if $author}
	<p class="author">{t author=$author->getName()|h escape=no}Posted by <em>%1</em>{/t}</p>
{/if}

{!$news->getBody()|markdown} 

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
