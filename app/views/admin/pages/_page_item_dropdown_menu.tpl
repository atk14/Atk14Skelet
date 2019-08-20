{if is_null($pull)}{assign pull right}{/if}
{dropdown_menu pull=$pull}
	{a action=edit id=$page}{!"pencil-alt"|icon} {t}Edit{/t}{/a}
	{a namespace="" action="pages/detail" id=$page}{!"eye"|icon} {t}Visit public link{/t}{/a}

	{if $page->isDeletable()}
		{capture assign="confirm"}{t 1=$page->getTitle()|h escape=no}You are about to delete page %1
Are you sure?{/t}{/capture}
		{a_remote action=destroy id=$page _method=post _confirm=$confirm}{!"trash-alt"|icon} {t}Delete page{/t}{/a_remote}
	{/if}
{/dropdown_menu}
