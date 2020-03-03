{assign var=gallery_items value=$gallery->getGalleryItems()}

{dropdown_menu clearfix=false}
	{a action="edit" id=$gallery}{!"edit"|icon} {t}Edit{/t}{/a}
{/dropdown_menu}

<h1>{$page_title}</h1>

<p>
{!$gallery->getDescription()|h|default:"<em>{t}bez popisu{/t}</em>"}
</p>


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

<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
    <span class="sr-only">0%</span>
  </div>
</div>

<p>{a action="gallery_items/create_new" gallery_id=$gallery _class="btn btn-default js--image_to_gallery_link"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Add an image{/t}{/a}</p>

</div>

{*
<h3>{t}Přidat fotografii{/t}</h3>

{render partial="shared/form" form=$create_item_form}
*}

