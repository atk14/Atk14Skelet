{if $context_menu && !$context_menu->isEmpty()}
	<div id="menu_context"{if !$context_menu->hasTitle()} class="no_title"{/if}>
		{if $context_menu->hasTitle()}<h3>{$context_menu->title}</h3>{/if}
		<ul>
			{render partial="shared/context_menu_item" from=$context_menu->getItems() item=item}
		</ul>
	</div>
{/if}
