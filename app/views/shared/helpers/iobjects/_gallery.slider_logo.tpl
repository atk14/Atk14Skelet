<div class="iobject iobject--gallery">
{capture assign=title}
{if $gallery->isTitleVisible()}{$gallery->getTitle()}{/if}
{/capture}
{capture assign=gallery_description}
{if $gallery->isTitleVisible()}{$gallery->getDescription()}{/if}
{/capture}

{render partial="shared/logo_slider" images=$gallery->getGalleryItems() photo_gallery_title=$title show_captions=false slides_per_view=6}

</div>
