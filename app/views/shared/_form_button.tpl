{*
 * Renders a submit button
 *
 * {render partial="shared/button_text"}
 * {render partial="shared/button_text" button_text="Send message"}
 *
 * Available variables:
 * - $class: String with optional html classes taken from {render} helper.
 *   Example: {render partial="shared/form_button" class="my-class another-class"}
 *}
{if empty($button_text) && $form}
	{assign var=button_text value=$form->get_button_text()}
{/if}

{if empty($class)}
	{if $form && $form->get_method()=="get"}
		{assign var=class value="btn btn-default"}
	{else}
		{assign var=class value="btn btn-primary"}
	{/if}
{/if}

<div class="form-group">
	<button type="submit" class="{$class}">{$button_text}</button>
</div>
