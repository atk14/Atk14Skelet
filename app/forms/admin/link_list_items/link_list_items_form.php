<?php
class LinkListItemsForm extends AdminForm {

	function set_up() {
		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Title"),
		)));

		$this->add_field("url", new CharField(array(
			"label" => _("URL / path"),
			"help_text" => _("An internal link should be set as an URI (e.g. /about-us/). An external link has to be set in the full format (e.g. https://example.com/file.pdf)."),
			"max_length" => 1000,
		)));

		$this->add_field("image_url", new PupiqImageField([
			"label" => _("Image"),
			"required" => false,
			"help_text" => _("In some cases the link looks better with an image."),
		]));

		$this->add_visible_field();
	}
}
