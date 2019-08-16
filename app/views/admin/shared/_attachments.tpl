{*
 * Vyrenderuje seznam priloh pro dany object.
 *
 * {render partial="shared/attachments" object=$page}
 * {render partial="shared/attachments" object=$product section="manuals" section_title="Manuals & brochures"}
 *}

{if !$section_title}{assign section_title "{t}Attachments{/t}"}{/if}
{if !$empty_list_message}{assign empty_list_message "{t}Currently there are no attachments{/t}"}{/if}

{assign var=attachments value=Attachment::GetAttachments($object,$section)}

<h3 id="attachments">{button_create_new action="attachments/create_new" table_name=$object->getTableName() record_id=$object->getId() section=$section return_to_anchor=attachments}{t}Add an attachment{/t}{/button_create_new} {$section_title}</h3>

{if !$attachments}

	<p>{t}Currently there are no attachments.{/t}</p>

{else}

	<ul class="list-group list-group-attachments list-sortable" data-sortable-url="{link_to action="attachments/set_rank"}">
		{if $attachments}
			{render partial="shared/attachment_item" from=$attachments item=attachment}
		{/if}
	</ul>

{/if}
