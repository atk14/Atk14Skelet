<h1>{button_create_new link_list_id=$link_list}{t}Add a link to the list{/t}{/button_create_new} {$page_title}</h1>

{if !$link_list_items}

	<p>{t}There is no link in the list.{/t}</p>

{else}

	<ul class="list-group list-sortable" data-sortable-url="{link_to action="set_rank"}">
		{foreach $link_list_items as $link_list_item}
			<li class="list-group-item" data-id="{$link_list_item->getId()}">
				{$link_list_item->getTitle()}
				(<a href="{$link_list_item->getUrl()}">{$link_list_item->getUrl()}</a>)
				{dropdown_menu}
					{a action=edit id=$link_list_item}{!"edit"|icon} {t}Edit{/t}{/a}
					{a_destroy id=$link_list_item}{!"remove"|icon} {t}Delete{/t}{/a_destroy}
				{/dropdown_menu}
			</li>
		{/foreach}
	</ul>

{/if}
