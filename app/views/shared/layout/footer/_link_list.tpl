{remove_if_contains_no_text}

{if $link_list && ($link_list->getVisibleItems() || ($logged_user && $logged_user->isAdmin()))}
	<div class="col-12 col-sm-6 col-md-3">

	{admin_menu for=$link_list align=left}
	{if $link_list->getTitle()}
		<h5>{$link_list->getTitle()}</h5>
	{/if}

	<ul class="list-unstyled">
		{foreach $link_list->getVisibleItems() as $item}
			<li{if $item->getCssClass()} class="{$item->getCssClass()}"{/if}>
				<a href="{$item->getUrl()}">{$item->getTitle()}</a>
			</li>
		{/foreach}
	</ul>

	</div>

{/if}

{/remove_if_contains_no_text}
