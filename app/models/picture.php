<?php
class Picture extends Iobject{

	use TraitPupiqImage;

	static function GetTranslatableFields(){ return ["title", "description", "alt"]; }

	function getPreviewImageUrl(){ return $this->getUrl(); }

	function getImageUrl(){ return $this->getUrl(); }

	function getName( $lang=null ) { return $this->getTitle($lang); }

	function getAlt($lang = null){
		$alt = (string)parent::getAlt($lang);
		if(strlen($alt)){
			return $alt;
		}
		if($this->isTitleVisible()){
			return $this->getTitle();
		}
		return "";
	}
}
