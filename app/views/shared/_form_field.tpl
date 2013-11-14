{*
 * Displays a form field.
 *
 * Examples:
 *   {render partial="shared/form_field" field="title"}
 *   {render partial="shared/form_field" field=$form->get_field("title")}
 *
 *   Printing out more fields on a single line
 *   {render partial="shared/form_field" fields="firstname,lastname,email"}
 *
 * Available variables:
 * - $field: Initially field's name. Later field object.
 * - $field->hint: Hint text for filling the field. Usually used as an input's
     placeholder attribute. DEPRECATED, use $field->hints array instead.
 * - $field->hints: Array of hint texts for filling the field. Usually used as an input's
     placeholder attribute. You're free to render it according to your needs.
 * - $field->hint_in_placeholder: Boolean indicating if hint was already used as a
     placeholder.
 * - $field->required: Boolean indicating if the field is required. By default
 *   it's used for handling presence of field's required attribute. You can use
 *   it for setting additional classes in your markup for example.
 * - $field->help_text: Information text related to field.
 * - $field->errors(): Returns an array of error messages resulting form failed
 *   validation.
 * - $class: String with optional html classes taken from {render} helper.
 *   Example: {render partial="shared/form_field" class="my-class another-class"}
 *}

{if $field}

	{if is_string($field)}
		{if !$form->has_field($field)}
			{error_log}The form doesn't contain field {$field}{/error_log}
		{/if}
		{assign var=field value=$form->get_field($field)}
	{/if}

	{assign var=is_checkbox value=$field->widget->input_type=="checkbox"}

	{if $is_checkbox}
		<div class="checkbox">
			<label for="{$field->id_for_label()}">
				{!$field->as_widget()} {$field->label}
			</label>
		</div>
	{else}
		<div class="form-group{if $field->required} form-group-required{/if}{if $field->errors() || $class} {trim}{if $field->errors()} has-error{/if}{if $class} {$class}{/if}{/trim}{/if}">

			<label for="{$field->id_for_label()}" class="control-label">{$field->label}</label>
			{!$field->as_widget()}

			{if $field->help_text}
				<div class="help-block">{!$field->help_text}</div>
			{/if}

			{if $field->errors()}
				<ul class="help-block">
					{foreach from=$field->errors() item=err_item}
						<li>{!$err_item}</li>
					{/foreach}
				</ul>
			{/if}

			{if $field->hints && !$field->hint_in_placeholder}
				<div class="help-hint hidden" data-title="{t}Examples:{/t}">
					<ul class="list-unstyled">
						{foreach $field->hints as $hint}
							<li>{!$hint}</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>
	{/if}

{else}

	{if isset($fields) && is_string($fields)}
		{assign var=fields value=","|explode:$fields} {* using PHP function as a smarty modifier! *}
	{/if}

	{if isset($fields) && is_array($fields)}
		{foreach from=$fields item=field}
			{render partial="shared/form_field" field=$field}
		{/foreach}

	{/if}

{/if}
