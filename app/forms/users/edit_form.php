<?php
class EditForm extends UsersForm{
	function set_up(){
		$this->_add_basic_account_fields();

		$this->enable_csrf_protection();
	}
}
