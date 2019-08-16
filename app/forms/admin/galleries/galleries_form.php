<?php
require_once(__DIR__ . "/../iobjects_form.php");

class GalleriesForm extends IobjectsForm{

	function set_up(){
		$this->add_iobjects_common_fields();

		/*
		$this->add_field("title", new CharField(array(
			"label" => _("Titulek"),
			"required" => false,
			"max_legth" => 255
		)));

		$this->add_field("description", new CharField(array(
			"label" => _("Popis"),
			"required" => false,
		)));
		*/
	}
}
