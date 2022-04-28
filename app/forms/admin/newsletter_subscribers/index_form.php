<?php
class IndexForm extends AdminForm{

	function set_up(){
		global $ATK14_GLOBAL;

		$this->add_search_field();

		$choices = ["" => "-- ".(_("language"))." --"];
		foreach($ATK14_GLOBAL->getSupportedLangs() as $lang){
			$choices[$lang] = $lang;
		}
		$this->add_field("language",new ChoiceField([
			"label" => _("Language"),
			"choices" => $choices,
			"required" => false,
		]));
	}
}
