<h2>{$paget_title}</h2>

{form}
{render partial="shared/form_error"}
<fieldset>
	<legend>{t}Current Password{/t}</legend>
	{render partial="shared/form_field" field="current_password"}
</fieldset>

<fieldset>
	<legend>{t}New Password{/t}</legend>
	{render partial="shared/form_field" fields="password,password_repeat"}
</fieldset>

<fieldset>
	<div class="buttons">
		<button type="submit">{t}Set new password{/t}</button>
	</div>
</fieldset>
{/form}
