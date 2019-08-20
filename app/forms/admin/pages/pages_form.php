<?php
class PagesForm extends AdminForm {

	function set_up() {

		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Title"),
		)));

		$this->add_translatable_field("teaser", new MarkdownField(array(
			"label" => _("Teaser"),
			"required" => false,
		)));

		$this->add_translatable_field("body", new MarkdownField(array(
			"label" => _("Body"),
		)));

		$this->add_field("parent_page_id", new PageField(array(
			"label" => _("Parent page"),
			"required" => false,
			"page_id" => isset($this->controller->page) ? $this->controller->page : null,
		)));

		$this->add_field("indexable", new BooleanField([
			"label" => _("Show in sitemap?"),
			"required" => false,
			"initial" => true,
		]));

		$this->add_field("visible", new BooleanField([
			"label" => _("Visible?"),
			"required" => false,
			"initial" => true,
		]));

		$this->add_code_field();
	}

	function clean() {
		$d = $this->cleaned_data;

		if (isset($d["parent_page_id"]) && isset($this->controller->page) && ($d["parent_page_id"]->getId()==$this->controller->page->getId())) {
			$this->set_error("parent_page_id", _("Pro aktuální stránku nelze použít stejnou stránku jako nadřízenou."));
		}
		return array(null, $d);
	}
}
