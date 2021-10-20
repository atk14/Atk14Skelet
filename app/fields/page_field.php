<?php
class PageField extends ChoiceField {

	function __construct($options = array()) {
		$choices = array("" => "-- "._("page")." --");
		$conditions = $bind_ar = array();

		foreach(Page::FindAll("parent_page_id",null) as $page){
			$choices[$page->getId()] = $page->getTitle();
			$this->_append_child_pages_to_choices($choices,$page,"");
		}

		$options["choices"] = $choices;
		parent::__construct($options);
	}

	function _append_child_pages_to_choices(&$choices,$parent_page,$prefix){
		static $nbsp;
		if(is_null($nbsp)){ $nbsp = html_entity_decode("&nbsp;"); }

		$pages = $parent_page->getChildPages();
		for($i=0;$i<sizeof($pages);$i++){
			$page = $pages[$i];
			$first = ($i === 0);
			$last = ($i === sizeof($pages) - 1);

			if($last){
				$my_prefix = "└";
			}else{
				$my_prefix = "├";
			}

			$choices[$page->getId()] = $prefix . $nbsp . $my_prefix . $nbsp . $page->getTitle();
			$this->_append_child_pages_to_choices($choices,$page,$prefix . $nbsp . ($last ? $nbsp : "│") . $nbsp);
		}
	}

	function clean($value) {
		list($err, $value) = parent::clean($value);

		if (!is_null($err)) {
			return array($err,$value);
		}
		if (is_null($value)) {
			return array(null,null);
		}
		if (is_null($_sp = Page::FindById($value))) {
			return array(_("There is no such page"), null);
		}
		return array(null, $_sp);
	}
}
