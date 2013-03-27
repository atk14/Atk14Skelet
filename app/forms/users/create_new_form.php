<?php
class CreateNewForm extends ApplicationForm{
	function set_up(){
		$this->add_field("login", new CharField(array(
			"label" => _("Username (login)"),
			"max_length" => 255,
		)));

		$this->add_field("name", new CharField(array(
			"label" => _("Your name"),
			"max_length" => 255,
		)));

		$this->add_field("email", new EmailField(array(
			"label" => _("E-mail address"),
			"max_length" => 255,
		)));

		$this->add_field("password", new PasswordField(array(
			"label" => _("Password"),
			"max_length" => 255,
		)));

		$this->add_field("password_repeat", new PasswordField(array(
			"label" => _("Password (repeat)"),
			"max_length" => 255,
		)));
	
		$this->enable_csrf_protection();
	}

	function clean(){
		$d = &$this->cleaned_data;
		if(!$this->has_errors()){
			if(User::FindByLogin($d["login"])){
				$this->set_error("login",_("This username has been already taken"));
			}

			if($d["password"]!=$d["password_repeat"]){
				$this->set_error("password_repeat",_("Password doesn't match"));
			}

			unset($d["password_repeat"]);
		}
		return array(null,$d);
	}
}
