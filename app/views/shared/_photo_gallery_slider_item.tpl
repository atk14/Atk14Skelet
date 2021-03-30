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