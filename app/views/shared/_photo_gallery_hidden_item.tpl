<figure class="gallery__item d-none" data-gallery_item_id="{$image->getId()}">
  <a href="{$image|img_url:$geometry_detail}" data-pswp-width="{$image|img_width:$geometry_detail}" data-pswp-height="{$image|img_height:$geometry_detail}"></a>
  <figcaption{if $image->getName()=="" && $image->getDescription()==""} class="d-none"{/if}>
    <div class="gallery-item__title">{$image->getName()}</div>
    <div class="gallery-item__description">{$image->getDescription()}</div>
  </figcaption>
</figure>