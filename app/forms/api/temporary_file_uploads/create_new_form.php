<?php
class CreateNewForm extends ApiForm {

	function set_up(){
		$this->add_field("file", new FileField(array(

		)));
	}
}
