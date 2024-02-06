<?php
// Traita pro Image a Picture
trait TraitPupiqImage {

	protected $_pupiq;

	function toString(){ return (string)$this->getUrl(); }

	/**
	 *	echo $image->getWidth(); // 800
	 *	echo $image->getWidth("80x80"); // 80
	 */
	function getWidth($transformation = null){
		if(!$transformation){ $transformation = (string)ARTICLE_BODY_MAX_WIDTH; }
		$pupiq = $this->_getPupiq();
		$pupiq->setTransformation($transformation);
		return $pupiq->getWidth();
	}

	/**
	 *	echo $image->getHeight(); // 600
	 *	echo $image->getHeight("80x80"); // 60
	 */
	function getHeight($transformation = null){
		if(!$transformation){ $transformation = (string)ARTICLE_BODY_MAX_WIDTH; }
		$pupiq = $this->_getPupiq();
		$pupiq->setTransformation($transformation);
		return $pupiq->getHeight();
	}

	function getOriginalWidth(){
		return $this->_getPupiq()->getOriginalWidth();
	}

	function getOriginalHeight(){
		return $this->_getPupiq()->getOriginalHeight();
	}

	function getUrl($transformation = null){
		if(is_null($transformation) && defined("ARTICLE_BODY_MAX_WIDTH") && constant("ARTICLE_BODY_MAX_WIDTH")>0){
			$transformation = constant("ARTICLE_BODY_MAX_WIDTH");
		}
		$pupiq = $this->_getPupiq();
		$url = $pupiq->getUrl($transformation);
		if(!$url){ $url = $this->_getUrl(); }
		return $url;
	}

	function _getUrl(){
		$field = $this->hasKey("image_url") ? "image_url" : "url"; // GalleryItem has image_url; Image or Picture has url
		return $this->g($field);
	}

	function photoswipeData($options = []) {
		$options += [
			'detail' => '1280x800',
			'thumbnail' => '120x120'
		];

		$pupiq = $this->_getPupiq();

		return [
			'w' => $this->getWidth(),
			'h' => $this->getHeight(),
			'src' => $pupiq->getUrl($options['detail']),
			'msrc' => $pupiq->getUrl($options['thumbnail']),
		];
	}

	function _getPupiq(){
		if(!isset($this->_pupiq)){
			$this->_pupiq = new Pupiq($this->_getUrl());
		}
		return $this->_pupiq;
	}
}
