<?php
require_once(__DIR__."/../users/users_form.php");
class RecoveryForm extends UsersForm{
	function set_up(){
		$this->_add_password_fields();

		$this->set_button_text(_("Set new password"));
	}
}
