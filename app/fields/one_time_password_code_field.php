<?php
class OneTimePasswordCodeField extends CharField {

	function __construct($options = []){
		$options += [
			"label" => _("Numeric code"),
			"max_length" => 20,
		];

		parent::__construct($options);
	}

	function clean($value){
		list($err,$value) = parent::clean($value);
		if(isset($err) || is_null($value)){ return [$err,$value]; }

		$value = preg_replace('/\s/','',$value);

		return [null,$value];
	}
}
