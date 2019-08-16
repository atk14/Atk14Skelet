<?php
class Gallery extends Iobject{

	function getGalleryItems(){
		return GalleryItem::FindAll("gallery_id",$this);
	}

	function getImages($geometry=null) {
		return array_map(function($v) { return $v->getImage(); }, $this->getGalleryItems());
	}

	function getPreviewImageUrl(){
		if($items = $this->getGalleryItems()){
			return $items[0]->getImageUrl();
		}
	}
}
