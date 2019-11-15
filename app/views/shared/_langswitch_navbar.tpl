{* Langswitch for use in navbars. For standalone use in navbar, see _langswitch *}
{if $supported_languages}

<li class="nav-item dropdown langswitch">
	<a class="nav-link dropdown-toggle" type="button" id="langswitch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{t}Change language{/t}">
		<img src="{$public}/dist/images/languages/{$current_language.lang}.svg" class="langswitch-flag" alt="{$current_language.name|capitalize}">
		{$current_language.name|capitalize}
		<span class="caret"></span>
	</a>
	<div class="dropdown-menu" aria-labelledby="langswitch">
		{foreach $supported_languages as $l}
				<a href="{$l.switch_url}" class="dropdown-item">
					<img src="{$public}/dist/images/languages/{$l.lang}.svg" class="langswitch-flag" alt="{$l.name|capitalize}">
					{$l.name|capitalize}
				</a>
		{/foreach}
	</div>
</li>

{/if}
