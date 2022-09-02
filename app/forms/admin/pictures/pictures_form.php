<?php
require_once(__DIR__ . "/../iobjects_form.php");

class PicturesForm extends IobjectsForm{

	function set_up(){
		$this->add_field("url", new PupiqImageField(array(
			"label" => _("Obrázek"),
		)));
		$this->add_iobjects_common_fields(array(
			"extra_themes" => array(
				"alternative" => _("Alternative look"),
				"card" => _("Card")
			),
		));
		$this->add_translatable_field("alt", new CharField([
			"label" => _("Alt"),
			"help_text" => _("Alternate text for the image, if the image cannot be displayed."),
			"required" => false,
		]));
	}
}
