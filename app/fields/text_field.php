<?php
/**
 * TextField renders <textarea> HTML tag
 * and preserves white spaces by default.
 */
class TextField extends CharField{
	function __construct($options = array()){
		$options = array_merge(array(
			"widget" => new TextArea(),
			"trim_value" => false,
		),$options);
		parent::__construct($options);
	}

	function clean($value){
		if($this->required && trim($value)==""){
			// when there are white characters only, the value is considered as empty
			return array($this->messages["required"],null);
		}
		return parent::clean($value);
	}
}
