<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$this->add_field("login",new LoginField());
		$this->add_field("password",new PasswordField());

		parent::set_up();
	}
}
