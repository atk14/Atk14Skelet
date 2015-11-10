<?php
class IndexForm extends AdminForm{
	function set_up(){
		$this->add_field("search",new SearchField(array(
			"lable" => _("Search"),
			"required" => false,
		)));

		$this->set_button_text(_("Search tags"));
	}
}
