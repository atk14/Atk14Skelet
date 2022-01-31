<?php
class Article extends ApplicationModel implements Translatable, iSlug {

	use TraitTags;
	
	static function GetTranslatableFields() { return array("title", "teaser", "body", "page_title", "page_description");}

	function getSlugPattern($lang){ return $this->g("title_$lang"); }

	function getPageTitle($lang = null){
		$out = parent::getPageTitle($lang);
		if(strlen($out)){ return $out; }
		return $this->getTitle($lang);
	}

	function getPageDescription($lang = null){
		$out = parent::getPageDescription($lang);
		if(strlen($out)){ return $out; }
		$out = $this->getTeaser($lang);
		if(strlen($out)){
			$out = Markdown($out);
			$out = String4::ToObject($out)->stripHtml()->toString();
			return $out;
		}
	}


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

	protected function _getNextArticle($newer,$tag_required = null){
		$conditions = $bind_ar = array();
		$conditions[] = "published_at<:now";
		$bind_ar[":now"] = now();
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
