<?php
// Traita pro Image a Picture
trait TraitPupiqImage {

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
		$pupiq = $this->_getPupiq();
		$url = $pupiq->getUrl($transformation);
		return $url ? $url : $this->g("url");
	}

	function photoswipeData($options = []) {
		$options += [
			'detail' => '1280x800',
			'thumbnail' => '120x120'
		];

		return [
			'w' => $this->getWidth(),
			'h' => $this->getHeight(),
			'src' => $this->getUrl($options['detail']),
			'msrc' => $this->getUrl($options['thumbnail']),
		];
	}

	function _getPupiq(){
		if(!isset($this->_pupiq)){
			$this->_pupiq = new Pupiq($this->g("url"));
		}
		return $this->_pupiq;
	}
}
