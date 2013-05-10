<?php
class SearchInput extends TextInput{
	var $input_type = 'search';

	function __construct($options = array()){
		$options += array(
			"attrs" => array()
		);

		$options["attrs"] += array(
			"class" => "search-query"
		);

		parent::__construct($options);
	}
}
