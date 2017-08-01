<?php
class Article extends ApplicationModel{
	function isPublished(){
		return strtotime($this->getPublishedAt())<time();
	}

	function getAuthor(){ return User::GetInstanceById($this->getAuthorId()); }

	function getNewerArticle($tag_required = null){
		return $this->_getNextArticle(true,$tag_required);
	}

	function getOlderArticle($tag_required = null){
		return $this->_getNextArticle(false,$tag_required);
	}

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

	protected function _getNextArticle($newer,$tag_required = null){
		$conditions = $bind_ar = array();
		$conditions[] = "published_at<NOW()";
		if($newer){
			$conditions[] = "published_at>:published_at OR (published_at=:published_at AND id>:id)";
		}else{
			$conditions[] = "published_at<:published_at OR (published_at=:published_at AND id<:id)";
		}
		$bind_ar[":published_at"] = $this->getPublishedAt();
		$bind_ar[":id"] = $this->getId();

		if($tag_required){
			$conditions[] = "id IN (SELECT article_id FROM article_tags WHERE tag_id=:tag_id)";
			$bind_ar[":tag_id"] = $tag_required;
		}	

		return Article::FindFirst(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => $newer ? "published_at" : "published_at DESC",
		));
	}
}
