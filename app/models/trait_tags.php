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
		return $this->getTagsLister()->setRecords($tags);
	}

	function addTag($tag){
		return $this->getTagsLister()->add($tag);
	}

	function getPrimaryTag(){
		if($tags = $this->getTags()){
			return $tags[0];
		}
	}

	function containsTag($tag){
		return $this->getTagsLister()->contains($tag);
	}
}
