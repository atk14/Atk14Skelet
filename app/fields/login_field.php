<?php
class LoginField extends RegexField{
	function __construct($options = array()){
		$options += array(
			"max_length" => 50,
			"help_text" => _("Only letters, numbers, dots and dashes are allowed. Up to 50 characters."),
			"hints" => array("john.doe","samantha92"),
			"check_for_conflicted_user_existence" => true,
		);

		$this->check_for_conflicted_user_existence = $options["check_for_conflicted_user_existence"];
		unset($options["check_for_conflicted_user_existence"]);

		parent::__construct('/^[a-z0-9.-]+$/',$options);

		$this->widget->attrs["pattern"] = '^[a-z0-9.-]+$';
		$this->update_messages(array(
			"login_taken" => _("This username has been already taken"),
		));
	}

	function clean($value){
		list($err,$value) = parent::clean($value);
		if($err || is_null($value)){ return array($err,$value); }

		if($this->check_for_conflicted_user_existence && User::FindFirst("login",$value)){
			$err = $this->messages["login_taken"];
			$value = null;
		}

		return array($err,$value);
	}
}
