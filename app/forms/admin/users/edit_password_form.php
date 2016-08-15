<?php
class EditPasswordForm extends AdminForm{
	function set_up(){
		$this->add_field("password",new CharField(array(
			"label" => _("New password"),
			"max_length" => 255,
		)));

		$this->enable_csrf_protection();

		$this->set_button_text(_("Set password"));
	}
}
