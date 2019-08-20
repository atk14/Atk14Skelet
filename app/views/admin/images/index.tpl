<h1>{button_create_new table_name=$table_name record_id=$record_id section=$section}{t}Add image{/t}{/button_create_new} {$page_title}</h1>

{if !$images}

	<p>{t}Currently there are no images.{/t}</p>

{else}

	<ul class="list-group list-sortable" data-sortable-url="{link_to action="set_rank"}">
		{render partial="image_item" from=$images item=image}
	</ul>

{/if}
