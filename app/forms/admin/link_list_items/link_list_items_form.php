<?php
class LinkListItemsForm extends AdminForm {

	function set_up() {
		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Title"),
		)));

		$this->add_field("url", new CharField(array(
			"label" => _("URL / path"),
			"required" => false,
			"help_text" => _("Interní odkaz zadávejte pouze ve formě URI (např. /napoveda/). Odkaz na externí web musí obsahovat celou cestu včetně domény (např. http://www.externi-web.cz/soubor.pdf). Nevyplněné URL znemená, že se zobrazí jen text bez odkazu - na kliknutí nebude reagováno."),
		)));

		$this->add_field("image_url", new PupiqImageField([
			"label" => _("Images"),
			"required" => false,
			"help_text" => _("In some cases the link looks better with an image."),
		]));
	}
}
