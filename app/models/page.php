<?php
class Page extends ApplicationModel implements Translatable, Rankable, iSlug {

	use TraitGetInstanceByCode;

	static function GetTranslatableFields() { return array("title", "teaser", "body", "page_title", "page_description"); }

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

	function getRootPage(){
		$root = $this;
		while($_root = $root->getParentPage()){
			$root = $_root;
		}
		return $root;
	}

	function getChildPages() {
		static $CACHE;
		if(is_null($CACHE)){
			$CACHE = [];
			$rows = $this->dbmole->selectRows("SELECT parent_page_id, id FROM pages ORDER BY rank, id");
			foreach($rows as $row){
				$parent_page_id = $row["parent_page_id"];
				$id = $row["id"];
				if(!isset($CACHE[$parent_page_id])){ $CACHE[$parent_page_id] = []; }
				$CACHE[$parent_page_id][] = $id;
			}
		}
		if(!isset($CACHE[$this->getId()])){
			return [];
		}
		return Cache::Get("Page",$CACHE[$this->getId()]);
	}

	function getVisibleChildPages(){
		$pages = $this->getChildPages();
		$pages = array_filter($pages,function($page){ return $page->isVisible(); });
		$pages = array_values($pages);
		return $pages;
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

	function isDeletable() {
		return !$this->hasSubpages();
	}

	function isVisible(){
		return $this->getVisible();
	}

	function isIndexable($check_parents = true){
		$page = $this;
		while($page){
			if(!$page->g("indexable")){ return false; }
			if(!$check_parents){ return true; }
			$page = $page->getParentPage();
		}
		return true;
	}

	function setRank($new_rank){
		return $this->_setRank($new_rank,array(
			"parent_page_id" => $this->g("parent_page_id"),
		));
	}
}
