{*
 * Renders a form in generic way
 *
 * Usefull for scaffolding
 *
 * {render partial="shared/form"}
 * {render partial="shared/form" form=$search_form}
 * {render partial="shared/form" form=$search_form button_text="Search"}
 * {render partial="shared/form" form=$search_form button_text="Search" small_form=1}
 *}
{form}

{render partial="shared/form_error"}
<fieldset>
	{render partial="shared/form_field" fields=$form->get_field_keys()}
	{render partial="shared/form_button"}
</fieldset>

{/form}
