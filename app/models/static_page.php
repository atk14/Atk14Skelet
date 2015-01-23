<?php
class StaticPage extends ApplicationModel implements Translatable {

	static $automatically_sluggable = true;

	static function GetTranslatableFields() { return array("title", "teaser", "body"); }

	/**
	 * $page_company = StaticPage::GetInstanceByPath("company");
	 * $page_management = StaticPage::GetInstanceByPath("company/management");
	 */
	static function GetInstanceByPath($path,&$lang = null){
		$orig_lang = $lang;

		$path = (string)$path;

		if(!$path){ return null; }
		
		$parent_static_page_id = null;
		foreach(explode('/',$path) as $slug){
			if(!$_sp = StaticPage::GetInstanceBySlug($slug,$lang,$parent_static_page_id)){
				$lang = $orig_lang; // we don't want to rewrite $lang unpredictable
				return null;
			}
			$parent_static_page_id = $_sp->getId();
		}
		return $_sp;
	}

	function getParentStaticPage() {
		return Cache::Get("StaticPage", $this->getParentStaticPageId());
	}

	function getChildStaticPages() {
		return StaticPage::FindAll("parent_static_page_id", $this);
	}

	function getSlugSegment(){
		return (string)$this->getParentStaticPageId();
	}

	function getSlugPattern($lang = null){
		return $this->getTitle($lang);
	}

	function getPath($lang = null){
		$slugs = array($this->getSlug($lang));
		$c = $this;
		while($p = $c->getParentStaticPage()){
			$slugs[] = $p->getSlug($lang);
			$c = $p;
		}
		$slugs = array_reverse($slugs);
		return join('/',$slugs);
	}

	function hasSubpages() {
		return sizeof($this->getChildStaticPages())>0;
	}

	function isDeletable() {
		return false;
	}
}
