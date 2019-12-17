<?php
class GalleryItem extends ApplicationModel implements Translatable, Rankable {

	use TraitPupiqImage;

	static function GetTranslatableFields(){ return array("title", "description"); }

	function setRank($new_rank){
		$this->_setRank($new_rank,array("gallery_id" => $this->getGalleryId()));
	}

	/**
	 * @return Pupiq
	 */
	function getLargeImage($geometry = "1920x1080"){
		$pupiq = new Pupiq($this->getImageUrl());
		$pupiq->setGeometry($geometry);
		return $pupiq;
	}

	function getImage() {
		return new Pupiq($this->getImageUrl());
	}

	function getName($lang=null) {
		return $this->getTitle($lang);
	}
}
