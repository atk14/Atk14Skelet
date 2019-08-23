{if $link_list && !$link_list->isEmpty()}
	<div class="col-12 col-sm-6 col-md-3">

	{if $link_list->getTitle()}
		<h5>{$link_list->getTitle()}</h5>
	{/if}

	<ul class="list-unstyled">
		{foreach $link_list->getItems() as $item}
			<li>
				<a href="{$item->getUrl()}">{$item->getTitle()}</a>
			</li>
		{/foreach}
	</ul>

	</div>

{/if}
