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
		{assign var=is_checkbox value=$form->fields.$key->widget->input_type=="checkbox"}
		{!$form|field:$key:"label_to_placeholder"}
		{if $is_checkbox}
			{$form->fields.$key->label}
		{/if}
		&nbsp;
	{/foreach}
	<button type="submit" class="btn btn-secondary">{!"search"|icon} {$button_text}</button>
{/form}
