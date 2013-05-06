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

{if !isset($small_form)}
	{assign var=small_form value=$form->is_small()}
{/if}

{if !$small_form}
	{assign var="layout" value="form-horizontal"}
{/if}

{form _novalidate="novalidate" _class=$layout}

{render partial="shared/form_error"}
<fieldset>
	{render partial="shared/form_field" fields=$form->get_field_keys() class="control-group"}
	{render partial="shared/form_button"}
</fieldset>

{/form}
