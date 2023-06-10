<?php
class UsersForm extends ApplicationForm{

	function _add_basic_account_fields(){
		$this->add_field("firstname", new CharField(array(
			"label" => _("Firstname"),
			"max_length" => 255,
		)));

		$this->add_field("lastname", new CharField(array(
			"label" => _("Lastname"),
			"max_length" => 255,
		)));

		$this->add_field("email", new EmailField(array(
			"label" => _("Email address"),
			"max_length" => 255,
			"initial" => "@",
			"help_text" => _("We will not disclose this address"),
		)));
	}

	function _add_password_fields(){
		$this->add_field("password", new PasswordField(array(
			"label" => _("Password"),
			"max_length" => 255,
		)));

		$this->add_field("password_repeat", new PasswordField(array(
			"label" => _("Password (repeat)"),
			"max_length" => 255,
		)));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(
			isset($d["password"]) &&
			isset($d["password_repeat"]) &&
			$d["password"]!==$d["password_repeat"]
		){
			$this->set_error("password_repeat",_("Password doesn't match"));
		}

		unset($d["password_repeat"]);

		return array($err,$d);
	}
}
