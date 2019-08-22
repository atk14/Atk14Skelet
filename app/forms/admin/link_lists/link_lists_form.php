<?php
class LinkListsForm extends AdminForm {

	function set_up() {

		$this->add_field("name", new CharField(array(
			"label" => _("Indicative title"),
			"help_text" => _("Name for administrative purposes. It helps localize the list. This title is not shown in the application."),
			"max_length" => 255,
		)));

		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Displayed title"),
			"help_text" => _("About us"),
			"max_length" => 255,
			"required" => false,
		)));

		$this->add_field("url", new CharField(array(
			"label" => _("URL / path"),
			"required" => false,
			"help_text" => _("Interní odkaz zadávejte pouze ve formě URI (např. /napoveda/). Odkaz na externí web musí obsahovat celou cestu včetně domény (např. http://www.externi-web.cz/soubor.pdf). Nevyplněné URL znemená, že se zobrazí jen text bez odkazu - na kliknutí nebude reagováno."),
		)));

		$this->add_code_field();
	}
}
