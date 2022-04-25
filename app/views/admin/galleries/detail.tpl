{assign var=gallery_items value=$gallery->getGalleryItems()}

{dropdown_menu clearfix=false}
	{a action="edit" id=$gallery}{!"edit"|icon} {t}Upravit fotogalerii{/t}{/a}
{/dropdown_menu}

<h1>{$page_title}</h1>

<p>
{!$gallery->getDescription()|h|default:"<em>{t}bez popisu{/t}</em>"}
</p>

<div class="drop-zone" data-dragdrop-hint="{t}Drag files here{/t}">

<h3>{t}Photos{/t}</h3>

<div class="js--image_gallery_wrap">

{render partial="shared/xhr_upload_image_form" url="{link_to action="gallery_items/create_new" gallery_id=$gallery _connector="&"}" label="{t}Přidat obrázky do fotogalerie{/t}"}

<ul class="list-group list-group-images list-sortable" data-sortable-url="{link_to action="gallery_items/set_rank"}">{trim}
	{render partial="gallery_item_item" from=$gallery->getGalleryItems()}
{/trim}</ul>

</div> {* class="js--image_gallery_wrap" *}

</div> {* class="drop-zone" *}

{*
<h3>{t}Přidat fotografii{/t}</h3>

{render partial="shared/form" form=$create_item_form}
*}

