<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$this->add_field("login", new CharField(array(
			"label" => _("Username (login)"),
			"max_length" => 255,
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
