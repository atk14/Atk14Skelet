<?php
class StaticPageField extends ChoiceField {
	function __construct($options=array()) {
		$choices = array("" => "-- "._("Stránka")." --");
		$conditions = $bind_ar = array();
		if ($options["static_page_id"]) {
			$conditions[] = "id!=:static_page_id";
			$bind_ar[":static_page_id"] = $options["static_page_id"];
		};

		foreach(StaticPage::FindAll(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => Translation::BuildOrderSqlForTranslatableField("static_pages","title")
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
		if (is_null($_sp = StaticPage::FindById($value))) {
			return array(_("Taková stránka neexistuje"), null);
		}
		return array(null, $_sp);
	}
}
