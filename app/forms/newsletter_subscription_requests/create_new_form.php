<?php
class CreateNewForm extends ApplicationForm {

	function set_up(){
		$this->add_field("email", new EmailField([
			"label" => _("E-mailová adresa"),
			"max_length" => 255,
		]));

		$this->_add_captcha_field();

		$this->set_button_text(_("Ověřit e-mailovou adresu"));
	}	
}
