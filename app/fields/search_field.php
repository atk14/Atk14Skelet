<?php
/**
 * Field for a search input :)
 */
class SearchField extends CharField{
	function __construct($options = array()){
		$options += array(
			"widget" => new SearchInput()
		);
		parent::__construct($options);
	}
}
