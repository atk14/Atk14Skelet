<?php
class LinkListsForm extends AdminForm {

	function set_up() {
		$this->add_field("system_name", new CharField(array(
			"label" => _("System title"),
			"help_text" => _("Useful in administration. This title is not shown in the application."),
			"max_length" => 255,
		)));

		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Displayed title"),
			"hint" => _("About us"),
			"max_length" => 255,
			"required" => false,
		)));

		$this->add_code_field();
	}
}
