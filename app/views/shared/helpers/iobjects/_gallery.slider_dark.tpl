<div class="iobject iobject--gallery">
{!$admin_menu}
{capture assign=title}
{if $gallery->isTitleVisible()}{$gallery->getTitle()}{/if}
{/capture}
{capture assign=gallery_description}
{if $gallery->isTitleVisible()}{$gallery->getDescription()}{/if}
{/capture}

{render partial="shared/photo_gallery_slider" images=$gallery->getGalleryItems() photo_gallery_title=$title style="dark" image_height=600 show_captions=true}

</div>
