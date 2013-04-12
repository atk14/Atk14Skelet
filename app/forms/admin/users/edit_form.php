<?php
class EditForm extends AdminForm{
	function set_up(){
		$this->add_field("name",new CharField(array(
			"label" => _("Name"),
			"max_length" => 255,
		)));

		$this->add_field("email",new EmailField(array(
			"label" => _("E-mail address"),
			"max_length" => 255,
		)));

		$this->add_field("is_admin",new BooleanField(array(
			"label" => _("Is admin?"),
			"required" => false,
		)));
	}
}
