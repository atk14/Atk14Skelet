<?php
class ExtendedPasswordFieldForm extends ApplicationForm {

	function set_up(){
		$this->add_field("password_1", new ExtendedPasswordField([
			"minimum_password_strength_required" => 80,
		]));

		$this->add_field("no_password_reveal", new ExtendedPasswordField([
			"minimum_password_strength_required" => 80,
			"enable_password_reveal" => false,
		]));

		$this->add_field("no_progressbar", new ExtendedPasswordField([
			"minimum_password_strength_required" => 80,
			"show_password_strength_progressbar" => false,
		]));

		$this->add_field("nothing", new ExtendedPasswordField([
			"enable_password_reveal" => false,
			"show_password_strength_progressbar" => false,
		]));
	}
}
