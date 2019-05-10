<?php
class TransformForm extends ApiForm{
	var $has_format_field = false;

	function set_up(){
		$this->add_field("source",new TextField(array(
			"max_length" => 399999,
			"required" => false,
		)));
	}
}
