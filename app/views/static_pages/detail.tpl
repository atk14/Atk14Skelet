<h1>{$static_page->getTitle()}</h1>

<div>
{!$static_page->getTeaser()}
</div>

<div>
{!$static_page->getBody()|markdown}
</div>

{if $child_pages}
	<hr>
	<h4>{t}Subpages{/t}</h4>
	<ul>
	{foreach $child_pages as $child_page}
		<li>{a action=detail id=$child_page}{$child_page->getTitle()}{/a}</li>
	{/foreach}
	</ul>
{/if}
