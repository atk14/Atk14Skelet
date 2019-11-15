{*
 *
 *	{render partial="shared/image_gallery" object=$brand}
 *	{render partial="shared/image_gallery" object=$brand section=""}
 *	{render partial="shared/image_gallery" object=$page section="logos" section_title="{t}Logos{/t}"}
 *}

{if !$section_title}{assign section_title "{t}Photo gallery{/t}"}{/if}
{if !$empty_list_message}{assign empty_list_message "{t}Currently there are no images{/t}"}{/if}

<h3>{$section_title}</h3>

<div class="js--image_gallery_wrap">

{assign var=images value=Image::GetImages($object,$section)}

{if !$images}
	<div class="img-message">
		<p>{$empty_list_message}</p>
	</div>
{/if}

<ul class="list-group list-group-images list-sortable" data-sortable-url="{link_to action="images/set_rank"}">
	{if $images}
		{render partial="shared/image_gallery_item" from=$images item=image}
	{/if}
</ul>

<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
    <span class="sr-only">60%</span>
  </div>
</div>

<p>{a action="images/create_new" table_name=$object->getTableName() record_id=$object->getId() section=$section _class="btn btn-default js--image_to_gallery_link"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Add an image{/t}{/a}</p>

</div>
