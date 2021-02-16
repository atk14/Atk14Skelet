{render partial="page_item_dropdown_menu" page=$page clearfix=false}

<h1>{$page_title}</h1>

{render partial="edit_form"}

<hr>

{render partial="shared/iobjects" object=$page}

<hr>

{render partial="shared/image_gallery" object=$page}

<hr>

<h3 id="subpages">{button_create_new parent_page_id=$page return_to_anchor="subpages"}{/button_create_new} {t}Subpages{/t}</h3>
{if $child_pages}
	<ul class="list-group list-sortable" data-sortable-url="{link_to action="pages/set_rank"}">
	{foreach $child_pages as $sp}
		<li class="list-group-item" data-id="{$sp->getId()}">
			{$sp->getTitle()}

			{render partial="page_item_dropdown_menu" page=$sp pull="right" show_edit_link=true}
		</li>
	{/foreach}
	</ul>
{else}
	<p>{t}This page has no subpages{/t}</p>	
{/if}
