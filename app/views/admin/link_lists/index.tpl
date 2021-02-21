<h1>{button_create_new}{t}Create new link list{/t}{/button_create_new} {$page_title}</h1>

{if !$link_lists}

	<p>{t}The list is empty.{/t}</p>

{else}

	<ul class="list-group list-group-flush list-sortable" data-sortable-url="{link_to action="set_rank"}">
		{render partial=link_list_item from=$link_lists}
	</ul>

{/if}
