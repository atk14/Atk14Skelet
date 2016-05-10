<?php
class CreateNewForm extends ApplicationForm{
	function set_up(){
		$this->add_field("login", new CharField(array(
			"label" => _("Login"),
			"max_length" => 255,
		)));
		$this->add_field("password", new PasswordField(array(
			"label" => _("Password"),
			"max_length" => 255,
		)));

		$this->set_button_text(_("Sign in"));

		$this->enable_csrf_protection();
	}
}
