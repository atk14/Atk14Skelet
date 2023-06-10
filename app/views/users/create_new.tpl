{render partial="shared/layout/content_header" title=$page_title}

<section class="border-top-0">
	{form _class="form-horizontal" _novalidate="novalidate"}
		{render partial="shared/form_error"}
		<fieldset>
			{render partial="shared/form_field" fields=$form->get_field_keys()}
			{render partial="shared/form_button"}
		</fieldset>
	{/form}
</section>
