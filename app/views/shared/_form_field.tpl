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
 * - $field->hints: Array of hint texts for filling the field. You're free to render it according to your needs.
 * - $field->required: Boolean indicating if the field is required. By default
 *   it's used for handling presence of field's required attribute. You can use
 *   it for setting additional classes in your markup for example.
 * - $field->help_text: Information text related to field.
 * - $field->errors(): Returns an array of error messages resulting form failed
 *   validation.
 * - $class: String with optional html classes taken from {render} helper.
 *   Example: {render partial="shared/form_field" class="my-class another-class"}
 * - $addon: String with optional html "input-group-addon" taken from {render} helper.
 *   More info: http://getbootstrap.com/components/#input-groups
 *   Example: {render partial="shared/form_field" addon="<span class='icon-example'></span>"}
 *}

{if $field}

	{if is_string($field)}
		{if !$form->has_field($field)}
			{error_log}The form doesn't contain field {$field}{/error_log}
		{/if}
		{assign var=field value=$form->get_field($field)}
	{/if}

	{assign var=is_checkbox value=$field->widget->input_type=="checkbox"}
	{assign var=is_hidden value=$field->widget->input_type=="hidden"}

	{capture assign=invalid_feedback}
		{if $field->errors()}
		<div class="feedback feedback--invalid">
			<ul class="list">
				{foreach from=$field->errors() item=err_item}
					<li class="list__item">{!"exclamation-circle"|icon} {!$err_item}</li>
				{/foreach}
			</ul>
		</div>
		{/if}
	{/capture}
	{capture assign=valid_feedback}
		{if $form->is_bound && !$field->errors()}
			<div class="feedback feedback--valid"><span class="sr-only">{t}OK{/t}</span></div>
		{/if}
	{/capture}

	{capture assign=tip_optional}
		{if !$form->is_bound && !$field->required}
			<small class="tip tip--optional">({t}Optional{/t})</small>
		{/if}
	{/capture}

	{capture assign=help_text}
		{if $field->help_text}
			<div class="form-text help-block">{!$field->help_text}</div>
		{/if}
	{/capture}

	{assign widget_options []}
	{assign render_helpblock !!true}
	{if $helptext_as=="popover"}
		{if $field->help_text}
			{if USING_BOOTSTRAP5}
				{assign widget_options ["attrs" => ["data-toggle" => "popover", "data-container" => "body", "data-placement" => "auto" , "data-fallbackPlacement" => "flip", "data-boundary" => "viewport", "data-content" => trim($field->help_text), "data-trigger" => "focus", "data-html" => "true"]]}
			{else}
				{assign widget_options ["attrs" => ["data-bs-toggle" => "popover", "data-bs-container" => "body", "data-bs-placement" => "auto" , "data-bs-fallbackPlacement" => "flip", "data-bs-boundary" => "viewport", "data-bs-content" => trim($field->help_text), "data-bs-trigger" => "focus", "data-bs-html" => "true"]]}
			{/if}
		{/if}
		{assign render_helpblock !!false}
	{/if}

	{capture assign=form_group_class}{normalize_css_class}
		form-group
		{if $is_checkbox}form-group--checkbox{/if}
		form-group--{$field->id_for_label()}
		{if $field->required}form-group--required{else}form-group--optional{/if}
		{if $field->errors()}form-group--has-error{/if}
		{if $form->is_bound && !$field->errors()}form-group--is-valid{/if}
		{$class}
	{/normalize_css_class}{/capture}

	{if $is_hidden}
		{!$field->as_widget()}
	{elseif $is_checkbox}
		<div class="{$form_group_class}">
			<div class="{if USING_BOOTSTRAP5}form-check form-switch{else}form-check custom-control custom-checkbox{/if}">
				{!$field->as_widget($widget_options)|customize_checkbox} {* helper customize_checkbox prida do checkboxu css tridu custom-control-input *}
				<label class="{if USING_BOOTSTRAP5}form-label{else}form-check-label custom-control-label{/if}" for="{$field->id_for_label()}">
					{$field->label}
				</label>
			</div>
			{if $render_helpblock==true}{!$help_text}{/if}
			{!$invalid_feedback}
			{!$valid_feedback}
		</div>
	{else}
		<div class="{$form_group_class}">
			{if !$no_label_rendering}
			<label for="{$field->id_for_label()}" class="{if USING_BOOTSTRAP5}form-label{else}control-label{/if}">{$field->label}{!$tip_optional}</label>
			{/if}
			{if $addon}
				<div class="input-group">
					<div class="input-group-addon">
						{!$addon}
					</div>
					{!$field->as_widget()}
				</div>
			{else}
				{!$field->as_widget($widget_options)}
			{/if}

			{if $render_helpblock==true}{!$help_text}{/if}

			{!$invalid_feedback}
			{!$valid_feedback}

			{if $field->hints}
				<div class="help-hint d-none" data-title="{t}Examples:{/t}" data-bs-title="{t}Examples:{/t}">
					<ul class="list pl-3">
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
