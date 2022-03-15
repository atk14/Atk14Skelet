<?php
class LinkListItem extends ApplicationModel implements Rankable, Translatable {

	use TraitUrlParams {
		getUrl as _getUrl;
	}

	static function GetTranslatableFields() {
		return array("title","url_localized");
	}

	function setRank($new_rank) {
		return $this->_setRank($new_rank, array(
			"link_list_id" => $this->getLinkList(),
		));
	}

	function getLinkList() {
		return Cache::Get("LinkList", $this->getLinkListId());
	}

	function isVisible(){ return $this->g("visible"); }

	/**
	 * Tries to determine the object this link is pointing to
	 *
	 *	$target = $item->getTargetObject();
	 *	if($target && is_a($target,"Page")){
	 *		// well, $target is a page :)
	 *  }
	 */
	function getTargetObject(){
		$params = $this->g("url_params");
		if($params){ $params = json_decode($params,true); }
		if(!$params){ return; }

		if($params["namespace"]!==""){ return; }
		switch("$params[controller]/$params[action]"){
			case "pages/detail":
				return Cache::Get("Page",(int)$params["id"]);
			case "main/index":
				return Page::GetInstanceByCode("homepage");
		}
	}

	/**
	 * Returns auto generated submenu
	 *
	 * Returns null when there is no submenu for this item.
	 *
	 * @return Menu14
	 */
	function getSubmenu($options = array()){
		$options += array(
			"reasonable_max_items_count" => null, // null will be returned when the count of submenu items exceeds this value
		);

		$params = $this->g("url_params");
		if($params){ $params = json_decode($params,true); }
		$target = $this->getTargetObject();

		$menu = new Menu14();

		if(strlen($code = $this->getCode()) && ($list = LinkList::GetInstanceByCode($code))){
			foreach($list->getVisibleItems() as $l_item){
				$item = $menu->addItem($l_item->getTitle(),$l_item->getUrl());
				$item->setMeta("image_url",$l_item->getImageUrl());
			}
		}elseif(is_a($target,"Page")){
			$menu->setMeta("image_url",$target->getImageUrl());
			foreach($target->getVisibleChildPages() as $chi){
				$item = $menu->addItem($chi->getTitle(),Atk14Url::BuildLink(["namespace" => "", "action" => "pages/detail", "id" => $chi]));
				$item->setMeta("image_url",$chi->getImageUrl());
			}
		}

		if($menu->isEmpty()){
			return null;
		}

		if($options["reasonable_max_items_count"] && sizeof($menu->getItems())>$options["reasonable_max_items_count"]){
			return null;
		}

		return $menu;
	}

	function getUrl($lang = null,$options = []){
		global $ATK14_GLOBAL;

		if(is_array($lang)){
			$options = $lang;
		}else{
			$options += [
				"lang" => $lang,
			];
		}

		$options += [
			"with_hostname" => false,
			"lang" => null,
		];

		$lang = $options["lang"];
		unset($options["lang"]);

		if(is_null($lang)){
			$lang = $ATK14_GLOBAL->getLang();
		}

		if(strlen($this->g("url_localized_$lang"))){
			return $this->g("url_localized_$lang");
		}

		return $this->_getUrl($lang,$options);
	}
}
