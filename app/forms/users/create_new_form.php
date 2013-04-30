<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$this->add_field("login", new RegexField('/^[a-z0-9.-]+$/',array(
			"label" => _("Username (login)"),
			"max_length" => 50,
			"help_text" => _("Only letters, numbers, dots and dashes are allowed. Up to 50 characters."),
			"hint" => "john.doe",
		)));
	
		$this->_add_basic_account_fields();

		$this->_add_password_fields();

		$this->enable_csrf_protection();
		$this->set_button_text(_("Register"));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(isset($d["login"]) && User::FindByLogin($d["login"])){
			$this->set_error("login",_("This username has been already taken"));
		}

		return array($err,$d);
	}
}
