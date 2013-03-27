<?php
class CreateNewForm extends ApplicationForm{
	function set_up(){
		$this->add_field("login", new CharField(array(	
			"max_length" => 255,
		)));
		$this->add_field("password", new PasswordField(array(
			"max_length" => 255,
		)));

		$this->enable_csrf_protection();
	}
}
