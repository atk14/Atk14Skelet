<?php
class ContactForm extends ApplicationForm{
	function set_up(){
		$this->add_field("name",new CharField(array(
			"label" => _("Your name"),
			"max_length" => 200,
		)));

		$this->add_field("email",new EmailField(array(
			"label" => _("Your e-mail"),
			"max_length" => 200,
		)));

		$this->add_field("body",new TextField(array(
			"label" => _("Text"),
			"max_length" => 2000,
		)));

		$this->enable_csrf_protection();
		$this->set_button_text(_("Send message"));
	}
}
