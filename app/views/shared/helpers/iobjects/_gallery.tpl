{assign var=gallery value=$iobject->getObject()}

<div style="margin: 1em 0;" class="clearfix">
{capture assign=title}
{if $gallery->isTitleVisible()}{$gallery->getTitle()}{/if}
{/capture}

	<div class="gallery" itemscope itemtype="http://schema.org/ImageGallery">
		{render partial="shared/photo_gallery" images=$gallery->getGalleryItems() photo_gallery_title=$title}
	</div>

</div>
