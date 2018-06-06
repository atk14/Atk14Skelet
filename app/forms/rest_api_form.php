<?php
class RestApiForm extends ApplicationForm{

	var $has_format_field = true;
	var $error_http_status_code = 400; // this HTTP status is returned from API in case that the form is invalid
	var $value_or_initial_fields = array(); // when some value is missing in an API call the field initial value can be set in cleaned data; e.g. ["country_id"]
	var $formats = array("json","jsonp","xml","yaml","html");

	function post_set_up(){
		$this->has_format_field && $this->add_format_field();

		foreach($this->fields as $key => &$f){
			$f->label = $key; // "Article Id" -> "article_id"; it is better for API
		}

		parent::post_set_up();
	}

	function add_format_field(){
		$this->add_field("format",new ChoiceField(array(
			"label" => "format",
			"initial" => "html",
			"choices" => array_combine($this->formats,$this->formats),
			"required" => true, 
		)));
	}

	function clean(){
		list($err,$cleaned_values) = parent::clean();

		$keys = array_keys($cleaned_values);
		foreach($this->value_or_initial_fields as $f){
			if(in_array($f,$keys) && !isset($cleaned_values[$f])){
				$cleaned_values[$f] = $this->get_initial($f);
			}
		}

		return array($err,$cleaned_values);
	}

}
