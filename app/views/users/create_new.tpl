<header>
	<h1>{$page_title}</h1>
</header>


<section class="border-top-0">
	{form _class="form-horizontal"}
		{render partial="shared/form_error"}
		<fieldset>
			{render partial="shared/form_field" fields=$form->get_field_keys()}
			{render partial="shared/form_button"}
		</fieldset>
	{/form}
</section>

<section>
	{form _class="" form=$form2}
		{render partial="shared/form_error" form=$form2}
		<fieldset>
			{render partial="shared/form_field" fields=$form2->get_field_keys() form=$form2}
			{render partial="shared/form_button"}
		</fieldset>
	{/form}
</section>