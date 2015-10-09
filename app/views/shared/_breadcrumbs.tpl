{if $breadcrumbs}
	<ol class="breadcrumb">
		{foreach $breadcrumbs->getItems() as $item}
			<li
				{if $item->class || $item->active} class="{trim}{if $item->class}{$item->class}{/if}{if $item->active} active{/if}{/trim}"{/if}
				{if $item->title}title="{$item->title}"{/if}
			>
				{if $item->url}<a href="{$item->url}">{/if}
					{$item->text}
				{if $item->url}</a>{/if}
			</li>
		{/foreach}
	</ol>		
{/if}
