<?php
class SearchInput extends TextInput{
	var $input_type = 'search';

	function SearchInput($options = array()){
		$options += array(
			"attrs" => array()
		);

		$options["attrs"] += array(
			"class" => "search-query"
		);

		parent::TextInput($options);
	}
}
