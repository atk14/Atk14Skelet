<?php
class BooleanWithSelectField extends ChoiceField {

	function __construct($options = []){
		$options["choices"] = [
			"" => "",
			"on" => _("Ano"),
			"off" => _("Ne"),
		];

		parent::__construct($options);
	}

	function format_initial_data($data){
		if(is_bool($data)){
			return $data ? "on" : "off";
		}
		return $data;
	}

	function clean($value){
		list($err,$value) = parent::clean($value);

		if(isset($err) || is_null($value)){
			return [$err,$value];
		}

		switch($value){
			case "on":
				$value = true;
				break;
			case "off":
				$value = false;
				break;
			default:
				$value = null;
				break;	
		}

		return [$err,$value];
	}
}
