{if $context_menu && !$context_menu->isEmpty()}
	<div id="menu_context">
		{if $context_menu->hasTitle()}
			<h3>{$context_menu->getTitle()}</h3>
		{/if}

		<ul class="nav nav-pills">
			{render partial="shared/context_menu_item" from=$context_menu->getItems() item=item}
		</ul>
	</div>
{/if}
