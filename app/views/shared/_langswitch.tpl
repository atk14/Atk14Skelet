{* Langswitch for standalone use. For use in navbar, see _langswitch_navbar *}
{if $supported_languages}

<div class="dropdown langswitch">
	<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="langswitch" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{t}Change language{/t}">
		<img src="{$public}/images/languages/{$current_language.lang}.svg" class="langswitch-flag" alt="{$current_language.name|capitalize}">
		{$current_language.name|capitalize}
		<span class="caret"></span>
	</button>
	<div class="dropdown-menu" aria-labelledby="langswitch">
		{foreach $supported_languages as $l}
				<a href="{$l.switch_url}" class="dropdown-item">
					<img src="{$public}/images/languages/{$l.lang}.svg" class="langswitch-flag" alt="{$l.name|capitalize}">
					{$l.name|capitalize}
				</a>
		{/foreach}
	</div>
</div>

{/if}
