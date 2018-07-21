<?php
class AdminForm extends ApplicationForm{

	function add_search_field($values = array()){
		$values += array(
			"label" => _("Search"),
			"required" => false,
		);

		$this->add_field("search",new SearchField($values));
	}
}
