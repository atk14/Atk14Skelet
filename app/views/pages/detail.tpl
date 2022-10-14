<article>
	{render partial="shared/layout/content_header" title=$page->getTitle() teaser=$page->getTeaser()|markdown}
	{admin_menu for=$page}
	<section class="page-body">
		{!$page->getBody()|markdown}
	</section>
	
</article>

{if $page->getCode()=="contact"}
	{render_component controller="contact_messages" action="create_new"}
{/if}

{if $child_pages}
	<section class="child-pages">
		{*<h4>{t}Subpages{/t}</h4>*}
		<ul class="list-unstyled">
		{foreach $child_pages as $child_page}
			<li>
				{a action=detail id=$child_page}
					{if $child_page->getImageUrl()}
						<img {!$child_page->getImageUrl()|img_attrs:"80x80"} alt="" class="img-thumbnail">
					{/if}
					{$child_page->getTitle()}
				{/a}
			</li>
		{/foreach}
		</ul>
	</section>
{/if}

