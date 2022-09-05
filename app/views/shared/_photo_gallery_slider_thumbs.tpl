{*
 * Photo gallery, ready to use Photoswipe
 * Usage:
 *
 *	{render partial="shared/photo_gallery" object=$brand}
 *	{render partial="shared/photo_gallery" object=$brand compact=true}
 *	or
 *	{render partial="shared/photo_gallery" images=$object->getImages()}
 *
 *	{render partial="shared/photo_gallery" object=$brand photo_gallery_title="Photo gallery" image_height=400 thumb_size="60"}
 * CAUTION: swiper--thumbnails must be placed in the markup BEFORE main slider otherwise sliders would not be synced.
 *}

{if !$images && $object}
	{assign var=images value=Image::GetImages($object)}
{/if}
{if !$image_height}
	{assign var=image_height value=400}
{/if}
{if !$thumb_size}
	{assign var=thumb_size value=90}
{/if}
{assign var=geometry_image value="x"|cat:$image_height}
{assign var=geometry_thumbnail value=$thumb_size|cat:"x"|cat:$thumb_size|cat:"xcrop"}
{assign geometry_detail "2000x1600"}
<div class="gallery__images">
	{if $images|count == 1}
		<section class="section--slider">
			<div class="swiper--images">
				{foreach $images as $image}
						{render partial="shared/photo_gallery_slider_item" image=$image}
				{/foreach}
			</div>
		</section>
	{else if $images}
		{assign uniqid uniqid()}
		<section class="section--slider section--slider--thumbnails">
			
			<div class="swiper swiper--thumbnails" data-slides_per_view="auto" data-loop="{$loop|default: false}" data-autoplay="false" data-slider_id="t_{$uniqid}" id="swiper_t_{$uniqid}"{if $breakpoint} data-breakpoint="{$breakpoint}"{/if}{if $centered_slides} data-centered_slides="{$centered_slides}"{/if} data-spacebetween="5">
				<div class="swiper-wrapper">
			
					{foreach $images as $image}
						<div class="swiper-slide slider-item-{$image@iteration-1}" style="width: auto">
							<picture>
								<source srcset="{!$image|img_url:$geometry_thumbnail}">
								<img {!$image|img_attrs:$geometry_thumbnail} alt="{$image->getName()}" class="img-fluid">
							</picture>
						</div>
					{/foreach}

				</div>
				
				<!-- If we need navigation buttons -->
				<div class="swiper-button-prev" id="swiper_button_prev_t_{$uniqid}"><span class="sr-only">{t}Previous{/t}</span></div>
				<div class="swiper-button-next" id="swiper_button_next_t_{$uniqid}"><span class="sr-only">{t}Next{/t}</span></div>
			</div>
			

			<div class="swiper swiper--images swiper--images--dark" data-slides_per_view="{$slides_per_view|default: 1}" data-loop="{$loop|default: false}" data-autoplay="false" data-slider_id="{$uniqid}" id="swiper_{$uniqid}"{if $breakpoint} data-breakpoint="{$breakpoint}"{/if}{if $centered_slides} data-centered_slides="{$centered_slides}"{/if} data-thumbs="#swiper_t_{$uniqid}">
				<div class="swiper-wrapper">

					{foreach $images as $image}
						{render partial="shared/photo_gallery_slider_item" image=$image}
					{/foreach}

				</div>

				<!-- If we need navigation buttons -->
				<div class="swiper-button-prev" id="swiper_button_prev_{$uniqid}"><span class="sr-only">{t}Previous{/t}</span></div>
				<div class="swiper-button-next" id="swiper_button_next_{$uniqid}"><span class="sr-only">{t}Next{/t}</span></div>
			</div>
			
			{* hidden large image links to be used with PhotoSwipe *}
			{foreach $images as $image}
				{render partial="shared/photo_gallery_hidden_item" image=$image}
			{/foreach}
		
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
</div>
