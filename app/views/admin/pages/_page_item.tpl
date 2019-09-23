<li class="list-group-item" data-id="{$page->getId()}">
	<div class="d-flex justify-content-between align-items-center">
	{$page->getTitle()|default:$mdash}
	{if strlen($page->getCode())}<small>{$page->getCode()}</small>{/if}
	{if !$page->isIndexable()}<em>({t}not showing in sitemap{/t})</em>{/if}
	{if !$page->isVisible()}<em>({t}invisible{/t})</em>{/if}

	{foreach $secondary_langs as $sl}
		{a namespace="" controller=pages action=detail id=$page lang=$sl}{t 1=$sl}[%1]{/t}{/a}
	{/foreach}

	{render partial="page_item_dropdown_menu" page=$page pull=""}
	</div>
	{if $page->getChildPages()}
		<ul class="list-group  list-group-flush list-sortable" data-sortable-url="{link_to action="pages/set_rank"}">
			{render partial="page_item" from=$page->getChildPages() item=page}
		</ul>
	{else}
		<div class="clearfix"></div>
	{/if}
</li>
