{*
 * Usage:
 *
 *	{render partial="shared/photo_gallery" object=$brand}
 *	or
 *	{render partial="shared/photo_gallery" images=$object->getImages()}
 *}

{if !isset($images) && $object}
	{assign var=images value=Image::GetImages($object)}
{/if}

{if $images}
	{if !isset($photo_gallery_title)}{capture assign="photo_gallery_title"}{t}Photo gallery{/t}{/capture}{/if}
	<section class="image-gallery">
		{if $photo_gallery_title}<h4>{$photo_gallery_title}</h4>{/if}
		<ul class="list-inline">
			{foreach $images as $image}
				<li class="list-inline-item mb-3">
					<a href="{$image|img_url:"1024"}" title="{if $image->getDescription()}{$image->getDescription()}{/if}">
						<img {!$image|img_attrs:"!200x200"} alt="{$image->getName()}" class="img-thumbnail">
					</a>
				</li>
			{/foreach}
		</ul>
	</section>
{/if}
