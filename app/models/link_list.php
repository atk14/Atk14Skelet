<?php
/**
 * Model class for a List of links
 */
class LinkList extends ApplicationModel implements Translatable, Rankable {

	use TraitGetInstanceByCode;

	static function GetTranslatableFields() {
		return array("title");
	}

	function setRank($rank){
		$this->_setRank($rank);
	}

	/**
	 *	$items = $list->getLinkListItems();
	 */
	function getLinkListItems() {
		$conditions = $bind_ar = [];

		$conditions[] = "link_list_id=:link_list";
		$bind_ar[":link_list"] = $this;

		return LinkListItem::FindAll("link_list_id",$this);
	}

	/**
	 * @alias
	 */
	function getItems(){
		return $this->getLinkListItems();
	}

	function getVisibleLinkListItems(){
		$items = $this->getLinkListItems();
		$items = array_filter($items,function($item){ return $item->isVisible(); });
		$items = array_values($items);
		return $items;
	}

	/**
	 * @alias
	 */
	function getVisibleItems(){
		return $this->getVisibleLinkListItems();
	}

	/**
	 *
	 *	$link_list->isEmpty();
	 *	$link_list->isEmpty(["consider_visibility" => true]); // this is default
	 *	$link_list->isEmpty(true);
	 */
	function isEmpty($options = array()){
		if(!is_array($options)){
			$options = array("consider_visibility" => $options);
		}
		$options += array(
			"consider_visibility" => true,
		);
		return sizeof($options["consider_visibility"] ? $this->getVisibleLinkListItems() : $this->getLinkListItems())==0;
	}

	/**
	 * Returns visible items packed as Menu14
	 * 
	 * @return Menu14|null
	 */
	function asSubmenu(){
		$submenu = new Menu14();
		foreach($this->getVisibleItems() as $l_item){
			$item = $submenu->addItem($l_item->getTitle(),$l_item->getUrl());
			$item->setMeta("image_url",$l_item->getImageUrl());
			$item->setMeta("css_class",$l_item->getCssClass());
			$item->setMeta("code",$l_item->getCode());
		}
		if($submenu->isEmpty()){
			return null;
		}
		return $submenu;
	}

	function isDeletable(){
		return true;
	}

	function destroy($destroy_for_real = null){
		foreach($this->getItems() as $item){
			$item->destroy($destroy_for_real);
		}

		return parent::destroy($destroy_for_real);
	}
}
