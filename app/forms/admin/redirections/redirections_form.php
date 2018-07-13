<?php
class RedirectionsForm extends AdminForm {

	function set_up(){
		$this->add_field("source_url", new CharField(array(
			"label" => _("Source URL"),
			"hints" => array(
				"http://example.com/document.pdf",
				"//example.com/document.pdf",
				"/document.pdf",
			),
		)));

		$this->add_field("target_url", new CharField(array(
			"label" => _("Target URL"),
			"hints" => array(
				"http://example.com/public/document.pdf",
				"//example.com/public/document.pdf",
				"/public/document.pdf",
			)
		)));

		$this->add_field("moved_permanently", new BooleanField(array(
			"label" => _("Moved permanently?"),
			"required" => false,
			"initial" => true,
		)));
	}
}
