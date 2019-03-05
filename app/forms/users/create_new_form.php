<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$this->add_field("login", new LoginField(array(
			"label" => _("Username (login)"),
		)));
	
		$this->_add_basic_account_fields();

		$this->_add_password_fields();

		if(defined("INVITATION_CODE_FOR_USER_REGISTRATION") && strlen(INVITATION_CODE_FOR_USER_REGISTRATION)){
			$this->add_field("invitation_code",new CharField(array(
				"required" => true,
				"max_length" => 200,
				"help_text" => _("In order to register you need to obtain an invitation code"),
			)));
		}

		$this->enable_csrf_protection();
		$this->set_button_text(_("Register"));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(defined("INVITATION_CODE_FOR_USER_REGISTRATION") && strlen(INVITATION_CODE_FOR_USER_REGISTRATION) && isset($d["invitation_code"]) && $d["invitation_code"]!==INVITATION_CODE_FOR_USER_REGISTRATION){
			$this->set_error("invitation_code",_("This is not a valid invitation code"));
		}
		unset($d["invitation_code"]);

		return array($err,$d);
	}
}
