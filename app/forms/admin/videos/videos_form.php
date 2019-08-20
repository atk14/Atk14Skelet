<?php
require_once(__DIR__ . "/../iobjects_form.php");

class VideosForm extends IobjectsForm {

	function set_up() {
		$this->add_iobjects_common_fields();
		$this->add_field("autoplay", new BooleanField([
			"label" => _("Automatické spuštění videa"),
			"required" => false,
		]));
		$this->add_field("loop", new BooleanField([
			"label" => _("Po ukončení přehrát znovu"),
			"required" => false,
		]));
	}

	function add_url_field(){
		$this->add_field("url", new OembedVideoField(array(
			"label" => _("URL videa"),
		)));
	}

	function add_image_url_field(){
		$this->add_field("image_url", new PupiqImageField(array(
			"label" => _("Náhledový obrázek"),
			"required" => false,
		)));
	}
}
