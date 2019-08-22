<?php
class LinkListItem extends ApplicationModel implements Rankable, Translatable {

	use TraitUrlParams;

	static function GetTranslatableFields() {
		return array("title");
	}

	function getLinkList() {
		return Cache::Get("LinkList", $this->getLinkListId());
	}

	function setRank($new_rank) {
		return $this->_setRank($new_rank, array(
			"link_list_id" => $this->getLinkList(),
		));
	}
}
