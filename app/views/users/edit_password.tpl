<h1>{$page_title}</h1>

{form}
{render partial="shared/form_error"}
<fieldset>
	<legend>{t}Current password{/t}</legend>
	{render partial="shared/form_field" field="current_password"}
</fieldset>

<fieldset>
	<legend>{t}New password{/t}</legend>
	{render partial="shared/form_field" fields="password,password_repeat"}
</fieldset>

<fieldset>
	{render partial="shared/form_button"}
</fieldset>
{/form}
