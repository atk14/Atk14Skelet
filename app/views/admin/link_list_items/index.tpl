<h1>{button_create_new link_list_id=$link_list}{t}Nový odkaz{/t}{/button_create_new} {$page_title}</h1>

{if $finder->isEmpty()}
	<p>{t}Seznam odkazů je prázdný{/t}</p>
{else}
	<ul class="list-group list-sortable" data-sortable-url="{link_to action="set_rank"}">
		{foreach $finder->getRecords() as $link_item}
			<li class="list-group-item" data-id="{$link_item->getId()}">
				{$link_item->getTitle()}
				(<a href="{$link_item->getUrl()}">{$link_item->getUrl()}</a>)
				{dropdown_menu}
					{a action=edit id=$link_item}{!"edit"|icon} {t}Edit{/t}{/a}
					{a_destroy id=$link_item}{!"remove"|icon} {t}Smazat{/t}{/a_destroy}
				{/dropdown_menu}
			</li>
		{/foreach}
	</ul>
{/if}
