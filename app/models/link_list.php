<?php
/**
 * Model class for a List of links
 */
class LinkList extends ApplicationModel implements Translatable {

	use TraitGetInstanceByCode;

	static function GetTranslatableFields() {
		return array("title");
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
}
