<?php
class IobjectsForm extends AdminForm {

	function set_up(){
		$this->add_iobjects_common_fields();
	}

	function add_iobjects_common_fields($options = array()){
		$options += array(
			"extra_themes" => array(),
		);

		$themes = array("default" => _("Default appearance"));
		$themes += $options["extra_themes"];

		$this->add_title_field();
		$this->add_title_visible_field();
		$this->add_description_field();

		if($themes>1){
			$this->add_theme_field($themes);
		}
	}

	function add_title_visible_field($options = array()){
		$options += array(
			"label" => _("Titulek viditelný na webu?"),
			"required" => false,
			"initial" => true,
		);
		$this->add_field("title_visible",new BooleanField($options));
	}

	/**
	 *
	 * 	$this->add_theme_field([
	 *		"default" => "Default appearance",
	 *		"metal" => "Metal look",
	 *	]);
	 */
	function add_theme_field($themes){
		$this->add_field("theme", new ChoiceField(array(
			"label" => _("Appearance"),
			"choices" => $themes,
		)));
	}

}
