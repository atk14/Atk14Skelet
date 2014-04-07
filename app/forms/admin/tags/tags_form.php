<?php
class TagsForm extends AdminForm{
	function set_up(){
		$this->add_field("tag", new CharField(array(
			"label" => _("Tag"),
			"max_length" => 255,
			"hint" => "rumors",
		)));
	}
}
