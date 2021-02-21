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
	function getItems() {
		return $this->getLinkListItems();
	}

	function getVisibleLinkListItems(){
		$items = $this->getLinkListItems();
		$items = array_filter($items,function($item){ return $item->isVisible(); });
		return $items;
	}

	/**
	 * @alias
	 */
	function getVisibleItems(){
		return $this->getVisibleLinkListItems();
	}

	function isEmpty($consider_visibility = true){
		return sizeof($consider_visibility ? $this->getVisibleLinkListItems() : $this->getLinkListItems())==0;
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
