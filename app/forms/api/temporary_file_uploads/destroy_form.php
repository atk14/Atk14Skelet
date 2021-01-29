<?php
class DestroyForm extends ApiForm {

	function set_up(){
		$this->add_field("token", new CharField([
			"max_length" => 255,
		]));
	}
}
