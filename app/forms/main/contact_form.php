<?php
class ContactForm extends ApplicationForm{
	function set_up(){
		$this->add_field("name",new CharField(array(
			"label" => _("Name"),
			"max_length" => 200,
		)));

		$this->add_field("email",new CharField(array(
			"label" => _("E-mail"),
			"max_length" => 200,
		)));

		$this->add_field("body",new TextField(array(
			"label" => _("Text"),
			"max_length" => 2000,
		)));

		$this->enable_csrf_protection();
	}
}
