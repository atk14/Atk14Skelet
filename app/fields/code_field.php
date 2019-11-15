<?php
class CodeField extends CharField {

	function __construct($options = []){
		$options += [
			"null_empty_output" => true,
			"max_length" => 255,
		];

		parent::__construct($options);
	}
}
