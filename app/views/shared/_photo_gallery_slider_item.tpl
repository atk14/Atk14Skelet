<div class="swiper-slide slider-item-{$image@iteration-1}" style="width: auto">
	<figure class="gallery__item js_gallery_trigger" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
		<a href="{$image|img_url:$geometry_detail}" title="{$image->getName()}" itemprop="contentUrl" data-preview_for="{$image->getId()}">
			<img {!$image|img_attrs:$geometry_image} alt="{$image->getName()}" class="img-fluid" itemprop="thumbnail">
		</a>
		<figcaption{if $image->getName()=="" && $image->getDescription()==""} class="d-none"{/if}>
			<div class="gallery-item__title">{$image->getName()}</div>
			<div class="gallery-item__description">{$image->getDescription()}</div>
		</figcaption>
	</figure>
</div>
