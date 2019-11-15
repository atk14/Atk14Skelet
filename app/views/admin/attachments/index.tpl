<h1>{button_create_new table_name=$table_name record_id=$record_id section=$section}{t}Add attachment{/t}{/button_create_new} {$page_title}</h1>

{if !$attachments}

	<p>{t}Currently there are no attachments.{/t}</p>

{else}

	<ul class="list-group list-sortable" data-sortable-url="{link_to action="set_rank"}">
		{render partial="attachment_item" from=$attachments item=attachment}
	</ul>

{/if}
