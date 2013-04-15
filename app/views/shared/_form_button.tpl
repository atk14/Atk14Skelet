{*
 * Renders a submit button
 *
 * {render partial="shared/button_text"}
 * {render partial="shared/button_text" button_text="Send message"}
 *}
{if !isset($button_text) && $form}{assign var=button_text value=$form->get_button_text()}{/if}
<div class="buttons">
	<button type="submit">{if $button_text}{$button_text}{else}{t}Save{/t}{/if}</button>
</div>
