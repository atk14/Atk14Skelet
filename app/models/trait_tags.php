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

	function getPrimaryTag(){
		if($tags = $this->getTags()){
			return $tags[0];
		}
	}
}
