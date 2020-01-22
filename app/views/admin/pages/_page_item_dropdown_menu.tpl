{if is_null($pull)}{assign pull right}{/if}
{dropdown_menu pull=$pull clearfix=$clearfix}
	{if $action=="index"}
	{a action=edit id=$page}{!"pencil-alt"|icon} {t}Edit{/t}{/a}
	{/if}

	{a namespace="" action="pages/detail" id=$page}{!"eye"|icon} {t}Visit public link{/t}{/a}
	{a action=create_new parent_page_id=$page}{!"plus"|icon} {t}Create a subpage{/t}{/a}

	{if $action=="index"}
	{if $page->isDeletable()}
		{capture assign="confirm"}{t 1=$page->getTitle()|h escape=no}You are about to delete page %1
Are you sure?{/t}{/capture}
		{a_remote action=destroy id=$page _method=post _confirm=$confirm}{!"trash-alt"|icon} {t}Delete page{/t}{/a_remote}
	{/if}
	{/if}
{/dropdown_menu}
