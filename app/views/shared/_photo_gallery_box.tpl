{*
 * Usage:
 *
 *	{render partial="shared/photo_gallery_box" object=$brand}
 *	{render partial="shared/photo_gallery_box" object=$brand compact=true}
 *	or
 *	{render partial="shared/photo_gallery_box" images=$object->getImages()}
		
 *}

{if !$images && $object}
	{assign var=images value=Image::GetImages($object)}
{/if}
{assign geometry_detail "1600"}
{assign var="geometry_thumb_portrait" "600x900xcrop"}
{assign var="geometry_thumb_landscape" "900x600xcrop"}
{assign var="geometry_thumb_square" "300x300xcrop"}
{assign var="geometry_thumb_bigsquare" "900x900xcrop"}
{assign var="geometry_thumb_half_h" "900x450xcrop"}
{assign var="geometry_thumb_half_v" "450x900xcrop"}
{assign var="geometry_thumb_half_square" "450x450xcrop"}
{assign var="geometry_thumb_mini" "32x32,format=png"}
{assign var="geometry_thumb_transition" "300x300"}

{if $images}
	
	{assign var="max_num_show" 4}
	{if $images|@count <= $max_num_show}
		{assign var="num_show" $images|@count}
	{else}
		{assign var="num_show" $max_num_show}
	{/if}
	
	{assign var="first_aspect_ratio"  $images[0]|img_width:$geometry_detail/$images[0]|img_height:$geometry_detail}
	{if $first_aspect_ratio < 1}
		{assign var="orientation" "portrait"}
	{else}
		{assign var="orientation" "landscape"}
	{/if}
	{assign var="num_remaining" $images|@count - $max_num_show}
		
	{if !isset($photo_gallery_title)}{capture assign="photo_gallery_title"}{t}Photo gallery{/t}{/capture}{/if}
	<section class="photo-gallery photo-gallery--square{if $compact} photo-gallery--compact{/if}">
		<div class="gallery__images orientation-{$orientation} num-{$num_show}" itemscope itemtype="http://schema.org/ImageGallery">
			{foreach $images as $image}
				{assign var="i" value=$image@iteration}
				{if $i == 1 }
					{* first image thumbnail landscape or portrait *}
					{if $orientation == "landscape" }
						{assign var="thumb_geometry" $geometry_thumb_landscape}
					{else}
						{assign var="thumb_geometry" $geometry_thumb_portrait}
					{/if}
				{else}
					{if $i > $max_num_show}
						{* hidden thumbnails must be there bc of photoswipe *}
						{assign var="thumb_geometry" $geometry_thumb_mini}
					{else}
						{* other visible thumbnails *}
						{assign var="thumb_geometry" $geometry_thumb_square}
					{/if}
				{/if}
				{if $num_show == 1 }
					{assign var="thumb_geometry" $geometry_thumb_bigsquare}
				{/if}
				{if $num_show == 2 }
					{if $orientation == "landscape" }
						{assign var="thumb_geometry" $geometry_thumb_half_h}
					{else}
						{assign var="thumb_geometry" $geometry_thumb_half_v}
					{/if}
				{/if}
				{if $num_show == 3 }
					{if $i == 1 }
						{if $orientation == "landscape" }
							{assign var="thumb_geometry" $geometry_thumb_half_h}
						{else}
							{assign var="thumb_geometry" $geometry_thumb_half_v}
						{/if}
					{else}
						{assign var="thumb_geometry" $geometry_thumb_half_square}
					{/if}
				{/if}
			
				<figure class="gallery__item{if $i > $max_num_show} d-none{/if}" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
					<a href="{$image|img_url:$geometry_detail}" title="{$image->getName()}" itemprop="contentUrl" data-minithumb="{$image|img_url:$geometry_thumb_transition}" data-pswp-width="{$image|img_width:$geometry_detail}" data-pswp-height="{$image|img_height:$geometry_detail}" itemprop="contentUrl" data-minithumb="{$image|img_url:$geometry_thumb_transition}">
						<picture>
							<source srcset="{!$image|img_url:$thumb_geometry}">
							<img {!$image|img_attrs:$thumb_geometry} alt="{$image->getName()}" class="img-fluid" itemprop="thumbnail">
						</picture>
					</a>
					<figcaption>
						<div class="gallery-item__title">{$image->getName()}</div>
						<div class="gallery-item__description">{$image->getDescription()}</div>
					</figcaption>
					{if $num_remaining > 0 && $i == $max_num_show}
						<div class="num-remaining"><span>+{$num_remaining}</span></div>
					{/if}
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
