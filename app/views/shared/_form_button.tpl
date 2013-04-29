{*
 * Renders a submit button
 *
 * {render partial="shared/button_text"}
 * {render partial="shared/button_text" button_text="Send message"}
 *}
{if empty($button_text) && $form}
	{assign var=button_text value=$form->get_button_text()}
{/if}

<div class="controls">
	<button type="submit" class="btn">{$button_text}</button>
</div>
