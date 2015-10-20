<?php
class MarkdownField extends TextField {
	function __construct($options = array()) {
		$options += array(
			"widget" => new TextArea(array(
				"attrs" => array(
					"data-provide" => "markdown",
				),
			)),
		);
		parent::__construct($options);
	}
}
