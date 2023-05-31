{*
 * Displays form`s error heading.
 *
 *	 {render partial="shared/form_error"}
 *	 {render partial="shared/form_error" form=$update_form}
 * 
 * If there is the small_form parameter,
 * no output will be produced unless there is a "non field" error.
 * In case of small forms this is enough that error fileds are highlighted.
 *  
 *	{render partial="shared/form_error" small_form=1}
 *}

{if !isset($small_form)}
	{assign var=small_form value=$form->is_small()}
{/if}

{if USING_BOOTSTRAP5}
	{capture assign="close_button"}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{t}Close{/t}"></button>{/capture}
{else}
	{capture assign="close_button"}<button type="button" class="close" data-dismiss="alert" aria-label="{t}Close{/t}">&times;</button>{/capture}
{/if}

{if $form->has_errors()}
		{if $form->non_field_errors()}
			{if sizeof($form->non_field_errors())>1}
				{* if there are more erorrs *}
				<div class="alert alert-danger alert-dismissible">
					<p>
						<em>{t}The following difficulties have occurred during the form processing:{/t}</em>
					</p>
					{!$close_button}
					<ul>
						{render partial="shared/form_error_item" from=$form->non_field_errors() item=error}
					</ul>
				</div>
			{else}
				{* if there is only one error *}
				{assign var=errors value=$form->non_field_errors()}
				<p class="alert alert-danger alert-dismissible">
					<em>{!$errors.0}</em>
					{!$close_button}
				</p>
			{/if}
		{elseif !$small_form}
			<p class="alert alert-danger alert-dismissible">
				<em>{t}Some of the items were filled incorrectly. Please, check the form and correct the errors.{/t}</em>
				{!$close_button}
			</p>
		{/if}
{/if}
