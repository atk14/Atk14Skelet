{*
 * Render a search form
 *
 * It is expected that the form contains just a single field: search
 *}

{if empty($button_text) && $form}
	{assign var=button_text value=$form->get_button_text()}
{/if}

{form _class="form-search"}
	{foreach $form->get_field_keys() as $key}
	{!$form.$key} {* e.g. $form.search *}
	{/foreach}
	<button type="submit" class="btn btn-secondary">{!"search"|icon} {$button_text}</button>
{/form}
