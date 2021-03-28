{*
 * Photo gallery, ready to use Photoswipe
 * Usage:
 *
 *	{render partial="shared/photo_gallery" object=$brand}
 *	{render partial="shared/photo_gallery" object=$brand compact=true}
 *	or
 *	{render partial="shared/photo_gallery" images=$object->getImages()}
 *
 *	{render partial="shared/photo_gallery" object=$brand photo_gallery_title="Photo gallery" style="dark" image_height=400}
 *}

{if !$images && $object}
	{assign var=images value=Image::GetImages($object)}
{/if}
{if !$image_height}
	{assign var=image_height value=400}
{/if}
{assign var=geometry_image value="x"|cat:$image_height}
{assign geometry_detail "2000x1600"}

{if $images}
	{assign uniqid uniqid()}
	<section class="section--slider">

		<div class="swiper-container swiper--images gallery__images{if $style=="dark"} swiper--images--dark{/if}" data-slides_per_view="{$slides_per_view|default: 1}" data-loop="{$loop|default: true}" data-autoplay="{$autoplay|default:6000}" data-slider_id="{$uniqid}" id="swiper_{$uniqid}"{if $breakpoint} data-breakpoint="{$breakpoint}"{/if}{if $centered_slides} data-centered_slides="{$centered_slides}"{/if}>
			<div class="swiper-wrapper">

				{foreach $images as $image}
					<div class="swiper-slide slider-item-{$image@iteration-1}" style="width: auto">
						<figure class="gallery__item" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
							<a href="{$image|img_url:$geometry_detail}" title="{$image->getName()}" data-size="{$image|img_width:$geometry_detail}x{$image|img_height:$geometry_detail}" itemprop="contentUrl">
								<img {!$image|img_attrs:$geometry_image} alt="{$image->getName()}" class="img-fluid" itemprop="thumbnail">
							</a>
							<figcaption{if $image->getName()=="" && $image->getDescription()==""} class="d-none"{/if}>
								<div><strong>{$image->getName()}</strong></div>
								<div>{$image->getDescription()}</div>
							</figcaption>
						</figure>
					</div>
				{/foreach}

			</div>

			<!-- If we need navigation buttons -->
			<div class="swiper-button-prev" id="swiper_button_prev_{$uniqid}"><span class="sr-only">{t}Previous{/t}</span></div>
			<div class="swiper-button-next" id="swiper_button_next_{$uniqid}"><span class="sr-only">{t}Next{/t}</span></div>
			<div class="container-fluid--fullwidth swiper-pagination" id="swiper_pagination_{$uniqid}"></div>
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
