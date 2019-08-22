<?php
class TagsForm extends AdminForm{

	function set_up(){
		$this->add_field("tag", new CharField(array(
			"label" => _("Tag"),
			"max_length" => 255,
			"hint" => "rumors",
		)));

		$this->add_translatable_field("tag_localized", new CharField([
			"label" => _("Tag lokalized"),
			"max_length" => 255,
			"required" => false,
			"help_text" => _("Enter proper value when the tag itself is not good in the given language"),
		]));

		$this->add_code_field();
	}
}
