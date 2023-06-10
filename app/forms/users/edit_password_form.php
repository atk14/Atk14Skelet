<?php
class EditPasswordForm extends UsersForm{
	function set_up(){
		$this->add_field("current_password",new PasswordField(array(
			"label" =>  _("Password"),
		)));
		$this->_add_password_fields();
		$this->set_button_text(_("Set new password"));

		$this->enable_csrf_protection();
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(!$this->has_errors() && $d["password"]==$d["current_password"]){
			$this->set_error(_("The new password is the same as your current password"));
		}

		return array($err,$d);
	}
}
