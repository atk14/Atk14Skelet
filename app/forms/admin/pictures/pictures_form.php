<?php
require_once(__DIR__ . "/../iobjects_form.php");

class PicturesForm extends IobjectsForm{

	function set_up(){
		$this->add_field("url", new PupiqImageField(array(
			"label" => _("Obrázek"),
		)));
		$this->add_iobjects_common_fields();
	}
}
