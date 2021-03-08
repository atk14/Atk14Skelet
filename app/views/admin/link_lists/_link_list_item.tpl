<li class="list-group-item" data-id="{$link_list->getId()}">
	<div class="d-flex justify-content-between align-items-center">
		<div>
			{$link_list->getSystemName()}<br>
			{$link_list->getTitle()}
		</div>
		
		{if strlen($link_list->getCode())}<small>{$link_list->getCode()}</small>{/if}

		<div>
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