<?php
class CreateNewForm extends UsersForm{
	function set_up(){
		$login_field = $this->add_field("login", new RegexField('/^[a-z0-9.-]+$/',array(
			"label" => _("Username (login)"),
			"max_length" => 50,
			"help_text" => _("Only letters, numbers, dots and dashes are allowed. Up to 50 characters."),
			"hint" => "john.doe",
		)));
		$login_field->widget->attrs["pattern"] = '^[a-z0-9.-]+$';
		$login_field->widget->attrs["title"] = _('lowercase letters, numbers, dots and dashes are expected');
	
		$this->_add_basic_account_fields();

		$this->_add_password_fields();

		$this->enable_csrf_protection();
		$this->set_button_text(_("Register"));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(isset($d["login"]) && User::FindByLogin($d["login"])){
			$this->set_error("login",_("This username has been already taken"));
		}

		return array($err,$d);
	}


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
}
