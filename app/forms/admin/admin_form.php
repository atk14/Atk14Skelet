<?php
class AdminForm extends ApplicationForm{

	/**
	 * Prida policka pro podporovane jazykove mutace.
	 *
	 * Parametry se pouzivaji stejne jako pro add_field.
	 * Uvnitr vola add_field pro kazdy podporovany jazyk.
	 *
	 * @param string $field_name identifikator formularoveho polcka
	 * @param Field $field
	 * @param array $additional_langs dalsi jazyky, ktere aplikace jinak nema aktivovane
	 */
	function add_translatable_field($field_name, $field, $additional_langs=array()) {
		global $ATK14_GLOBAL;
		$label_suffix = array(
			"cs" => _("cs"),
			"en" => _("en"),
			"sk" => _("sk"),
		);

		# pro pripad, ze mame vicejazycna policka s '_id' na konci nazvu.
		# napr. display_image_cz_id, display_image_en_id
		$id_suffix = "";
		if (preg_match("/(.+)?(_(id)?)$/", $field_name, $matches)) {
			$field_name = $matches[1];
			$id_suffix = $matches[2];
		}
		# k zakladnim jazykum pridame dalsi
		$langs = array_merge(array_keys($ATK14_GLOBAL->getConfig("locale")),$additional_langs);
		$label = $field->label;
		if (!$label) $label = $field_name;
		$clas = get_class($field);
		foreach($langs as $lang){
			$suffix = isset($label_suffix[$lang])?$label_suffix[$lang]:$lang;
			$lang_field = new $clas(array(
				"required" => $field->required,
				"label" => "$label [$suffix]",
				"initial" => $field->initial,
				"help_text" => $field->help_text,
				"hint" => $field->hint,
				"disabled" => $field->disabled,
				"widget" => $field->widget,
				"null_empty_output" => isset($field->null_empty_output)?$field->null_empty_output:false,
			));

			$this->add_field($field_name."_$lang".$id_suffix, $lang_field);
		}
	}

	function add_rank_field(){
		$this->add_field("rank",new RankField());
	}

	function has_storno_button(){
		if(isset($this->has_storno_button)){ return $this->has_storno_button; }
		return false;
#		return $this->get_method()=="post";
	}
}
