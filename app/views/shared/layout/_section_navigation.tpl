{* Sidebar vertical nav menu used in admin *}
	<button class="btn btn-light nav-section__toggle">{t}Menu{/t} {!"angle-down"|icon}</button>
	<div class="nav-section__collapsible">{* all things collapsible in mobile view go here *}
		<ul class="nav nav-pills flex-column {if count($section_navigation)<15} nav--sticky{/if}">
			{foreach $section_navigation as $item}
				<li class="nav-item">
					<a class="nav-link{if $item->isActive()} active{/if}" href="{$item->getUrl()}">{$item->getTitle()}</a>
				</li>
			{/foreach}
		</ul>
	</div>