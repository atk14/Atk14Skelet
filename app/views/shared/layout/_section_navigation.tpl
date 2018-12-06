<ul class="nav nav-pills flex-column">
	{foreach $section_navigation as $item}
		<li class="nav-item">
			<a class="nav-link{if $item->isActive()} active{/if}" href="{$item->getUrl()}">{$item->getTitle()}</a>
		</li>
	{/foreach}
</ul>
