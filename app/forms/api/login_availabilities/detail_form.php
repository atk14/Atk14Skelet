<?php
class DetailForm extends ApiForm{
	function set_up(){
		$this->add_field("login", new CharField(array(
			"hint" => "john.doe",
		)));
	}
}
