<?php
class DetailForm extends ApiForm{
	function _set_up(){
		$this->add_field("login", new CharField(array(
			"hint" => "john.doe",
		)));
	}
}
