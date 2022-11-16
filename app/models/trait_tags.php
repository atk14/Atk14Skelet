<?php
/**
 * Couple of methods for objects with tags
 */
trait TraitTags {

	function getTagsLister(){
		return $this->getLister("Tags");
	}

	function getTags(){
		return Cache::Get("Tag",$this->getTagsLister()->getRecordIds());
	}

	function setTags($tags){
		$tags = $this->_cleanTag($tags);
		return $this->getTagsLister()->setRecords($tags);
	}

	function addTag($tag){
		$tag = $this->_cleanTag($tag);
		return $this->getTagsLister()->add($tag);
	}

	function removeTag($tag){
		$tag = $this->_cleanTag($tag);
		return $this->getTagsLister()->remove($tag);
	}

	function getPrimaryTag(){
		if($tags = $this->getTags()){
			return $tags[0];
		}
	}

	/**
	 *
	 *	$article->containsTag($tag);
	 *	$article->containsTag($code); // e.g. "news"
	 *	$article->containsTag($id); // e.g. 123
	 */
	function containsTag($tag){
		$tag = $this->_cleanTag($tag);
		return $this->getTagsLister()->contains($tag);
	}

	/**
	 *
	 * @alias
	 */
	function hasTag($tag){
		return $this->containsTag($tag);
	}

	private function _cleanTag($tag){
		if(is_array($tag)){
			$out = array();
			foreach($tag as $k => $t){
				$out[$k] = $this->_cleanTag($t);
			}
			return $out;
		}

		if(is_numeric($tag) && ($t = Cache::Get("Tag",$tag))){
			$tag = $t;
		}elseif(!is_a($tag,"Tag") && ($t = Tag::GetInstanceByCode("$tag"))){
			$tag = $t;
		}
		return $tag;
	}
}
