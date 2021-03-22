<?php
class IobjectsForm extends AdminForm {

	function set_up(){
		$this->add_iobjects_common_fields();
	}

	function add_iobjects_common_fields(){
		$this->add_title_field();
		$this->add_title_visible_field();
		$this->add_description_field();
	}

	function add_title_visible_field($options = array()){
		$options += array(
			"label" => _("Titulek viditelný na webu?"),
			"required" => false,
			"initial" => true,
		);
		$this->add_field("title_visible",new BooleanField($options));
	}

}
