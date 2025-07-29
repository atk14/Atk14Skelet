{* Langswitch for use in navbars. For standalone use in navbar, see _langswitch *}
{if $supported_languages}

{assign uniqid ""|uniqid}

<li class="nav-item dropdown langswitch">
	<a href="#" class="nav-link dropdown-toggle" role="button" id="langswitch_{$uniqid}" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{t}Change language{/t}">
		<img src="{$public}dist/images/languages/{$current_language.lang}.svg" class="langswitch-flag" alt="{$current_language.name|capitalize}" width="24" height="15">
		{$current_language.name|capitalize}
		<span class="caret"></span>
	</a>
	<div class="dropdown-menu" aria-labelledby="langswitch_{$uniqid}">
		{foreach $supported_languages as $l}
				<a href="{$l.switch_url}" class="dropdown-item">
					<img src="{$public}dist/images/languages/{$l.lang}.svg" class="langswitch-flag" alt="{$l.name|capitalize}" width="24" height="15">
					{$l.name|capitalize}
				</a>
		{/foreach}
	</div>
</li>

{/if}
