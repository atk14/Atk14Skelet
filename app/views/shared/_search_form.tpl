{*
 * Render a search form
 *
 * It is expected that the form contains just a single field: search
 *}

{if empty($button_text) && $form}
	{assign var=button_text value=$form->get_button_text()}
{/if}

{form _class="form-search"}
	{!$form.search}
	<button type="submit" class="btn">{$button_text}</button>
{/form}
