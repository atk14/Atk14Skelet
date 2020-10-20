<?php
class IndexForm extends ApiForm{
	function set_up(){
		$this->add_field("q",new CharField(array(
			"help_text" => _("Search term"),
			"max_length" => 200,
		)));
	}
}
