<h1 class="page-header">{$page_title}</h1>

{form _data-validate_rules=$js_validator->get_rules()|@to_json _data-validate_messages=$js_validator->get_messages()|@to_json}
	<fieldset>
		{render partial="shared/form_field" fields=$form->get_field_keys()}
		{render partial="shared/form_button"}
	</fieldset>
{/form}
