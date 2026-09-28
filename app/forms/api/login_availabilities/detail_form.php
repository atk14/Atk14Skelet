<?php
class DetailForm extends LoginAvailabilitiesForm{
	function set_up(){
		$this->add_field("login", new CharField(array(
			"hint" => "john.doe",
		)));
	}
}
