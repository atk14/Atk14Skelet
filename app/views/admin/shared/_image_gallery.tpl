{*
 *
 *	{render partial="shared/image_gallery" object=$brand}
 *	{render partial="shared/image_gallery" object=$brand section=""}
 *	{render partial="shared/image_gallery" object=$page section="logos" section_title="{t}Logos{/t}"}
 *}

{if !$section_title}{assign section_title "{t}Photo gallery{/t}"}{/if}

<div class="drop-zone" data-dragdrop-hint="{t}Drag files here{/t}">

<h3>{$section_title}</h3>

<div class="js--image_gallery_wrap">

{render partial="shared/xhr_upload_image_form" url="{link_to action="images/create_new" table_name=$object->getTableName() record_id=$object->getId() section=$section _connector="&"}" label="{t}Přidat obrázky do fotogalerie{/t}"}

{assign var=images value=Image::GetImages($object,$section)}

<ul class="list-group list-group-images list-sortable" data-sortable-url="{link_to action="images/set_rank"}">{trim}
	{if $images}
		{render partial="shared/image_gallery_item" from=$images item=image}
	{/if}
{/trim}</ul>

</div> {* class="js--image_gallery_wrap" *}

</div> {* class="drop-zone" *}
