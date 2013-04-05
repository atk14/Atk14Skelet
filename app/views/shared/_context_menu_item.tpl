<li{if $item->active} class="active"{/if}{if !$item->isLink()} class="no_link"{/if}>
	{$item->getMarkup() nofilter}
</li>
