<?php
class AttachmentsForm extends AdminForm{

	function add_name_field($options = array()){
		$options += array(
			"label" => _("Title"),
			"hint" => _("User Manual"),
			"max_length" => 255,
			"required" => false,
		);
		$this->add_translatable_field("name",new CharField($options));
	}
}
