{assign var=gallery_items value=$gallery->getGalleryItems()}

{dropdown_menu clearfix=false}
	{a action="edit" id=$gallery}{!"edit"|icon} {t}Edit{/t}{/a}
{/dropdown_menu}

<h1>{$page_title}</h1>

<p>
{!$gallery->getDescription()|h|default:"<em>{t}bez popisu{/t}</em>"}
</p>

<div class="drop-zone">

<h3>{t}Photos{/t}</h3>

<div class="js--image_gallery_wrap">

{if !$gallery_items}

	<div class="img-message">
		<p>{t}Fotogalerie zatím neobsahuje žádný obrázek.{/t}</p>
	</div>

{/if}

<ul class="list-group list-group-images list-sortable" data-sortable-url="{link_to action="gallery_items/set_rank"}">
	{render partial="gallery_item_item" from=$gallery->getGalleryItems()}
</ul>

<br>

{render partial="shared/xhr_upload_image_form" url="{link_to action="gallery_items/create_new" gallery_id=$gallery _connector="&"}" label="{t}Přidat obrázky do fotogalerie{/t}"}

</div> {* class="js--image_gallery_wrap" *}

</div> {* class="drop-zone" *}

{*
<h3>{t}Přidat fotografii{/t}</h3>

{render partial="shared/form" form=$create_item_form}
*}

