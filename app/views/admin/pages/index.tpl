<h1>{button_create_new}{t}Add new page{/t}{/button_create_new} {$page_title}</h1>

{if !$root_pages}
	<p>{t}The list is empty.{/t}</p>
{else}

	<ul class="list-group list-group-flush list-sortable" data-sortable-url="{link_to action="pages/set_rank"}">
		{render partial="page_item" from=$root_pages item=page}
	</ul>
{/if}
