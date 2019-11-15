{form_remote _novalidate="novalidate" _class="form-horizontal" _role="form"}
	{render partial="shared/form_error"}
	<fieldset>
		{render partial="shared/form_field" fields=$form->get_field_keys()}

		<div class="form-group">
			<span class="button-container">
				<button type="submit" name="save_and_stay" value="1" class="btn btn-primary">{t}Save and continue editing{/t}</button>
				<button type="submit" class="btn btn-primary">{t}Save and go back{/t}</button>
			</span>
		</div>
	</fieldset>
{/form_remote}
