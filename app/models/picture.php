<?php
class Picture extends Iobject{

	use TraitPupiqImage;

	function getPreviewImageUrl(){ return $this->getUrl(); }

	function getImageUrl(){ return $this->getUrl(); }

	function getName( $lang=null ) { return $this->getTitle($lang); }
}
