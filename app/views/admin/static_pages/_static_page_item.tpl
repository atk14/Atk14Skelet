<li class="list-group-item{if $static_page->getChildStaticPages()} has-child{/if}">
	{$static_page->getTitle()}

	{a namespace="" controller=static_pages action=detail id=$static_page}{t 1=$lang}odkaz [%1]{/t}{/a}

	{foreach $secondary_langs as $sl}
		{a namespace="" controller=static_pages action=detail id=$static_page lang=$sl}{t 1=$sl}[%1]{/t}{/a}
	{/foreach}

	<div class="pull-right">
		{a action=edit id=$static_page}<span class="glyphicon glyphicon-edit"></span> {t}Upravit{/t}{/a}

		{if true || $static_page->isDeletable()}
			{capture assign="confirm"}{t 1=$static_page->getTitle()|h escape=no}Chystáte se smazat stránku %1
Jste si jistý/á?{/t}{/capture}
			{a_remote action=destroy id=$static_page _method=post _confirm=$confirm _title="{t}Smazat stránku{/t}" _class="btn btn-danger btn-xs"}<span class="glyphicon glyphicon-remove"></span>{/a_remote}
		{/if}
	</div>

	{if $static_page->getChildStaticPages()}
		<ul class="list-group">
			{render partial="static_page_item" from=$static_page->getChildStaticPages() item=static_page}
		</ul>
	{/if}
</li>
