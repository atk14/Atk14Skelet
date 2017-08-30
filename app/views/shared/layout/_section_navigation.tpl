<ul class="nav nav-pills nav-stacked">
	{foreach $section_navigation as $item}
		<li{if $item->isActive()} class="active"{/if}>
			<a href="{$item->getUrl()}">{$item->getTitle()}</a>
		</li>
	{/foreach}
</ul>
