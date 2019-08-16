<article>
	
	<header>
		{admin_menu for=$page}
		<h1>{$page->getTitle()}</h1>
		<div class="teaser">
		{!$page->getTeaser()|markdown}
		</div>
	</header>
	
	<section class="page-body">
		{!$page->getBody()|markdown}
	</section>
	
</article>

{if $child_pages}
	<section class="child-pages">
		<h4>{t}Subpages{/t}</h4>
		<ul>
		{foreach $child_pages as $child_page}
			<li>{a action=detail id=$child_page}{$child_page->getTitle()}{/a}</li>
		{/foreach}
		</ul>
	</section>
{/if}
