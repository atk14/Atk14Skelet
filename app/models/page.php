<?php
class Page extends ApplicationModel implements Translatable, Rankable, iSlug {

	use TraitGetInstanceByCode;

	static function GetTranslatableFields() { return array("title", "teaser", "body"); }

	/**
	 * $page_company = Page::GetInstanceByPath("company");
	 * $page_management = Page::GetInstanceByPath("company/management");
	 */
	static function GetInstanceByPath($path,&$lang = null){
		$orig_lang = $lang;

		$path = (string)$path;

		if(!$path){ return null; }
		
		$parent_page_id = null;
		foreach(explode('/',$path) as $slug){
			if(!$_sp = Page::GetInstanceBySlug($slug,$lang,$parent_page_id)){
				$lang = $orig_lang; // we don't want to rewrite $lang unpredictable
				return null;
			}
			$parent_page_id = $_sp->getId();
		}
		return $_sp;
	}

	function getParentPage() {
		return Cache::Get("Page", $this->getParentPageId());
	}

	function getChildPages() {
		return Page::FindAll("parent_page_id", $this);
	}

	function getSlugSegment(){
		return (string)$this->getParentPageId();
	}

	function getSlugPattern($lang){ return $this->g("title_$lang"); }

	function getPath($lang = null){
		$slugs = array($this->getSlug($lang));
		$c = $this;
		while($p = $c->getParentPage()){
			$slugs[] = $p->getSlug($lang);
			$c = $p;
		}
		$slugs = array_reverse($slugs);
		return join('/',$slugs);
	}

	function hasSubpages() {
		return sizeof($this->getChildPages())>0;
	}

	function isDeletable() {
		return !$this->hasSubpages();
	}

	function setRank($new_rank){
		return $this->_setRank($new_rank,array(
			"parent_page_id" => $this->g("parent_page_id"),
		));
	}
}
