<?php
class CreateNewForm extends ApplicationForm{
	function set_up(){
		$this->add_field("login",new CharField(array(
			"label" => _("Login or e-mail address"),
		)));
		$this->enable_csrf_protection();

		$this->set_button_text(_("Send me a recovery link"));
	}
}
