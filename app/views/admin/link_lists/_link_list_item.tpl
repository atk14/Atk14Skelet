<li class="list-group-item" data-id="{$link_list->getId()}">
	<div class="item__properties">
		<div class="item__title">
			{$link_list->getSystemName()}
			{if $link_list->getTitle() && $link_list->getTitle()!=$link_list->getSystemName()}
				<br>
				{$link_list->getTitle()}
			{/if}
		</div>
		<span class="item__code">
			{if $link_list->getCode()|strlen}{$link_list->getCode()}{/if}
		</span>
		<div>
			{if $link_list->getCode()|strlen && LinkListItem::GetInstanceByCode($link_list->getCode())}
				{assign linked_item LinkListItem::GetInstanceByCode($link_list->getCode())}
				<small>{t}This is a submenu for:{/t}</small><br>
				{a action="link_list_items/index" link_list_id=$linked_item->getLinkList()}{$linked_item->getLinkList()->getSystemName()}{/a} / {a action="link_list_items/edit" id=$linked_item}{$linked_item->getTitle()}{/a}
			{/if}
		</div>
		<div class="item__controls">
			{dropdown_menu}
				{a action="link_list_items/index" link_list_id=$link_list}{!"list"|icon} {t}Link list{/t}{/a}
				{a action=edit id=$link_list}{!"pencil-alt"|icon} {t}Edit{/t}{/a}

				{if $link_list->isDeletable()}
					{capture assign="confirm"}{t 1=$link_list->getSystemName()|h escape=no}You are about to permanently delete link list %1
Are you sure about that?{/t}{/capture}
					{a_destroy id=$link_list _confirm=$confirm}{!"trash-alt"|icon} {t}Delete{/t}{/a_destroy}
				{/if}
			{/dropdown_menu}
		</div>
	</div>
</li>
