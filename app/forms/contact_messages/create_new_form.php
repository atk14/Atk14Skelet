<?php
class CreateNewForm extends ApplicationForm{

	function set_up(){
		global $HTTP_REQUEST;

		$this->add_field("name",new CharField(array(
			"label" => _("Your name"),
			"max_length" => 200,
		)));

		$this->add_field("email",new EmailField(array(
			"label" => _("Your email"),
			"max_length" => 200,
		)));

		$this->add_sign_up_for_newsletter_field();

		$this->add_field("body",new TextField(array(
			"label" => _("Text"),
			"max_length" => 2000,
		)));

		if(defined("HCAPTCHA_SITE_KEY") && strlen(constant("HCAPTCHA_SITE_KEY"))>0 && defined("HCAPTCHA_SECRET_KEY") && strlen(constant("HCAPTCHA_SECRET_KEY"))>0){
			$this->add_field("captcha",new HcaptchaField(array(
				"label" => _("Spam protection"),
			)));
		}elseif(defined("RECAPTCHA_SITE_KEY") && strlen(constant("RECAPTCHA_SITE_KEY"))>0 && defined("RECAPTCHA_SECRET_KEY") && strlen(constant("RECAPTCHA_SECRET_KEY"))>0){
			$this->add_field("captcha",new RecaptchaField(array(
				"label" => _("Spam protection"),
			)));
		}

		$this->set_action($HTTP_REQUEST->getRequestUri()."#form_contact_messages_create_new");
		$this->enable_csrf_protection();
		$this->set_button_text(_("Send message"));
	}

	function clean(){
		list($err,$values) = parent::clean();

		// perhaps you may not want to have "captcha" in the cleaned data
		if(is_array($values)){ unset($values["captcha"]); }

		return array($err,$values);
	}
}
