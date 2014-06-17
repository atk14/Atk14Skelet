<?php
class IndexForm extends AdminForm{
	function set_up(){
		$this->add_field("search",new SearchField(array(
			"label" => _("Search"),
			"required" => false,
		)));
	}
}
