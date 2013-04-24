<li{if $item->active} class="active"{/if}{if !$item->isLink()} class="disabled"{/if}>
	{!$item->getMarkup()}
</li>
