<?php
class GalleryItemsForm extends AdminForm{

	function set_up(){
		$this->add_field("image_url",new PupiqImageField(array(
			"label" => _("Fotografie"),
		)));

		$this->add_translatable_field("title",new CharField(array(
			"label" => _("Titulek"),
			"max_length" => 255,
			"required" => false,
		)));

		$this->add_translatable_field("description",new CharField(array(
			"label" => _("Popis"),
			"max_length" => 1000,
			"required" => false,
		)));
	}
}
