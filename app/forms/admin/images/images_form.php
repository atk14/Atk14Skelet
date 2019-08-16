<?php
class ImagesForm extends AdminForm{

	function set_up(){
		$this->add_field("url", new PupiqImageField(array(
			"label" => _("Image"),
		)));

		$this->add_translatable_field("name",new CharField(array(
			"label "=> _("Title"),
			"required" => false,
			"max_length" => 255,
		)));

		$this->add_translatable_field("description",new CharField(array(
			"label "=> _("Description"),
			"required" => false,
			"max_length" => 1000,
		)));
	}

	function tune_for_product_image(){
		$this->add_field("display_on_card", new BooleanField(array(
			"label" => _("Display image in gallery on the product card?"),
			"required" => false,
			"initial" => true,
		)));
	}
}
