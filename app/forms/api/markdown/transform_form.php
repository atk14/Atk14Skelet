<?php
class TransformForm extends ApiForm{

	var $has_format_field = false;

	function set_up(){
		$this->add_field("source", new TextField(array(
			"max_length" => 50000,
			"required" => false,
		)));

		$this->add_field("base_href", new CharField([
			"max_length" => 255,
			"required" => false,
		]));

		$this->add_field("editor_preview", new BooleanField([
			"required" => false,
			"help_text" => _("Render for a preview in an editor (without admin menus)"),
		]));
	}
}
