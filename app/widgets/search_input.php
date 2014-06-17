<?php
class SearchInput extends TextInput{
	var $input_type = 'search'; //  renders <input type="search"...>

	function __construct($options = array()){
		/*
		$options += array(
			"attrs" => array()
		);

		$options["attrs"] += array(
			"class" => "text form-control search-query" // ... when one needs a special class (search-query)
		); */

		parent::__construct($options);
	}
}
