<?php
class StaticPageForm extends AdminForm {
	function set_up() {
		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Title"),
		)));
		$this->add_translatable_field("teaser", new TextField(array(
			"label" => _("Teaser"),
			"required" => false,
		)));
		$this->add_translatable_field("body", new TextField(array(
			"label" => _("Body"),
		)));

		$this->add_field("parent_static_page_id", new StaticPageField(array(
			"label" => _("Parent page"),
			"required" => false,
			"static_page_id" => isset($this->controller->static_page) ? $this->controller->static_page : null,
		)));
	}

	function clean() {
		$d = $this->cleaned_data;

		if (isset($d["parent_static_page_id"]) && isset($this->controller->static_page) && ($d["parent_static_page_id"]->getId()==$this->controller->static_page->getId())) {
			$this->set_error("parent_static_page_id", _("Pro aktuální stránku nelze použít stejnou stránku jako nadřízenou."));
		}
		return array(null, $d);
	}
}
