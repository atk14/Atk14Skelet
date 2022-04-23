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

		$this->add_translatable_field("url_localized", new UrlField([
			"label" => _("Specific URL for the given language"),
			"max_length" => 1000,
			"required" => false,
			"help_text" => _("If necessary, a specific URL can be specified for the given language."),
		]),["enable_live_translations" => false]);

		$this->add_field("css_class", new CharField(array(
			"label" => _("CSS class"),
			"max_length" => 255,
			"required" => false,
		)));

		$this->add_field("image_url", new PupiqImageField([
			"label" => _("Image"),
			"required" => false,
			"help_text" => _("In some cases the link looks better with an image."),
		]));

		$this->add_visible_field();

		$this->add_code_field();
	}
}
