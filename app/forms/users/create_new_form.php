<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$this->add_field("login", new LoginField(array(
			"label" => _("Username (login)"),
		)));
	
		$this->_add_basic_account_fields();

		$this->_add_password_fields();

		if(defined("INVITATION_CODE_FOR_USER_REGISTRATION") && strlen(INVITATION_CODE_FOR_USER_REGISTRATION)){
			$this->add_field("invitation_code",new CharField(array(
				"required" => true,
				"max_length" => 200,
				"help_text" => _("In order to register you need to obtain an invitation code"),
			)));
		}

		//

		$this->add_field("delivery_street", new CharField([
			"label" => "Street",
			"required" => false,
			"max_length" => 255,
		]));
		$this->add_field("delivery_city", new CharField([
			"label" => "City",
			"required" => false,
			"max_length" => 255,
		]));
		$this->add_field("delivery_zip", new CharField([
			"label" => "Zip",
			"required" => false,
			"max_length" => 255,
		]));

		$this->add_field("when_you_wake_up", new ChoiceField(array(
			"choices" => [
				"" => "- please select -",
				"morning" => "In the morning",
				"evening" => "In the evening",
			],
		)));
		
		$this->add_field("favourite_colors", new MultipleChoiceField(array(
			"choices" => ["red" => "Red", "green" => "Green", "blue" => "Blue", "black" => "Black", "orange" => "Orange"]
		)));

		$this->add_field("gender", new ChoiceField(array(
			"choices" => [
				"m" => "boy",
				"f" => "girl",
			],
			"widget" => new RadioSelect(),
		)));

		$this->add_field("interests", new MultipleChoiceField([
			"choices" => [
				"computers" => "Computers",
				"drinking" => "Drinking",
				"sport" => "Sport",
			],
			"required" => false,
			"widget" => new CheckboxSelectMultiple(),
		]));

		$this->add_field("notice", new TextField([
			"label" => _("Notice"),
			"required" => false,
		]));

		$this->add_field("confirmation", new ConfirmationField(array(
			"label" => _("I confirm everything"),
			"help_text" => _('You must confirm out terms & conditions at <a href="http://example.com/">example.com</a>'),
			"required" => false,
		)));

		$this->set_attr("novalidate","novalidate");
		$this->enable_csrf_protection();
		$this->set_button_text(_("Register"));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(defined("INVITATION_CODE_FOR_USER_REGISTRATION") && strlen(INVITATION_CODE_FOR_USER_REGISTRATION) && isset($d["invitation_code"]) && $d["invitation_code"]!==INVITATION_CODE_FOR_USER_REGISTRATION){
			$this->set_error("invitation_code",_("This is not a valid invitation code"));
		}
		unset($d["invitation_code"]);

		return array($err,$d);
	}


	/*
	function js_validator(){
		$js_v = parent::js_validator();

		//$js_v->validators["login"]->add_rule("remote",Atk14Url::BuildLink(array("controller" => "sign_up_js_validation", "action" => "check_login_availability")));
		//$js_v->validators["login"]->add_message("remote",_("The login has been already taken"));

		$js_v->validators["login"]->add_rule("remote",Atk14Url::BuildLink(array(
			"namespace" => "api",
			"controller" => "login_availabilities",
			"action" => "detail",
			"format" => "simple_boolean",
		))."&login=");
		$js_v->validators["login"]->add_message("remote",_("This login has been already taken"));
	
		return $js_v;
	}
	*/
}
