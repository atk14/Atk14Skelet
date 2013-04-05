<?php
require_once(dirname(__FILE__)."/../users/users_form.php");
class RecoveryForm extends UsersForm{
	function set_up(){
		$this->_add_password_fields();
	}
}
