<?php
class PageField extends ChoiceField {
	function __construct($options=array()) {
		$choices = array("" => "-- "._("page")." --");
		$conditions = $bind_ar = array();
		if ($options["page_id"]) {
			$conditions[] = "id!=:page_id";
			$bind_ar[":page_id"] = $options["page_id"];
		};

		foreach(Page::FindAll(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => Translation::BuildOrderSqlForTranslatableField("pages","title")
		)) as $_b) {
			$choices[$_b->getId()] = $_b->getTitle();
		}
		$options["choices"] = $choices;
		parent::__construct($options);
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
