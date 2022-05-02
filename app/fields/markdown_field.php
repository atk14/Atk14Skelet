<?php
class MarkdownField extends TextField {

	function __construct($options = array()) {
		$options += array(
			"base_href" => "", // e.g. "/admin/en/wiki_pages/"
		);

		$widget_attr = array();
		$widget_attr["data-provide"] = "markdown";

		$options += array(
			"widget" => new TextArea(array(
				"attrs" => array(
					"data-provide" => "markdown",
					"data-base_href" => $options["base_href"],
				),
			)),
		);

		unset($options["base_href"]);

		parent::__construct($options);
	}
}
