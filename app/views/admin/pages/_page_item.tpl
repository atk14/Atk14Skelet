<li class="list-group-item" data-id="{$page->getId()}">
	<div class="item__properties">
	<span class="item__title">{$page->getTitle()|default:$mdash}</span>
	<span class="item__code">{if strlen($page->getCode())}{$page->getCode()}{/if}</span>
	<span class="item__visibility-properties">
		{if !$page->isIndexable()}<span class="item__map-visibility">{!"stream"|icon} {t}not showing in sitemap{/t}</span>{/if}
		{if !$page->isVisible()}<span class="item__visibility">{!"eye-slash"|icon} {t}invisible{/t}</span>{/if}
	</span>
	<span class="item__languages">{trim}
	{foreach $secondary_langs as $sl}
		{a namespace="" controller=pages action=detail id=$page lang=$sl}{t 1=$sl}[%1]{/t}{/a}
	{/foreach}
	{/trim}</span>
	<div class="item__controls">
	{render partial="page_item_dropdown_menu" page=$page pull=""}
	</div>
	</div>
	{if $page->getChildPages()}
		<ul class="list-group  list-group-flush list-sortable" data-sortable-url="{link_to action="pages/set_rank"}">
			{render partial="page_item" from=$page->getChildPages() item=page}
		</ul>
	{else}
		<div class="clearfix"></div>
	{/if}
</li>
