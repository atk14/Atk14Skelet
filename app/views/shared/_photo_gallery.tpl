{*
 * Photo gallery, ready to use Photoswipe
 * Usage:
 *
 *	{render partial="shared/photo_gallery" object=$brand}
 *	{render partial="shared/photo_gallery" object=$brand compact=true}
 *	or
 *	{render partial="shared/photo_gallery" images=$object->getImages()}
 *
 *	{render partial="shared/photo_gallery" object=$brand photo_gallery_title="Photo gallery"}	
 *}

{if !$images && $object}
	{assign var=images value=Image::GetImages($object)}
{/if}
{assign geometry_detail "2000x1600"}

{if $images}
	{* {if !isset($photo_gallery_title)}{capture assign="photo_gallery_title"}{t}Photo gallery{/t}{/capture}{/if} *}
	<section class="photo-gallery{if $compact} photo-gallery--compact{/if}">
		<div class="gallery__images" itemscope itemtype="http://schema.org/ImageGallery">
			{foreach $images as $image}
				<figure class="gallery__item" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
					<a href="{$image|img_url:$geometry_detail}" title="{$image->getName()}" data-pswp-width="{$image|img_width:$geometry_detail}" data-pswp-height="{$image|img_height:$geometry_detail}" itemprop="contentUrl">
						<img {!$image|img_attrs:"x200"} alt="{$image->getName()}" class="" itemprop="thumbnail">
					</a>
					<figcaption>
						<div class="gallery-item__title">{$image->getName()}</div>
						<div class="gallery-item__description">{$image->getDescription()}</div>
					</figcaption>
			</figure>
			{/foreach}
		</div>
		{if $photo_gallery_title}
		<div class="gallery__caption iobject__caption">
			<div class="gallery__title iobject__title">{$photo_gallery_title}</div>
			{if $gallery_description}
			<div class="gallery__description iobject__description">{!$gallery_description}</div>
			{/if}
		</div>
		{/if}
	</section>
{/if}
