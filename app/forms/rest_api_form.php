<?php
class RestApiForm extends ApplicationForm{
	var $has_format_field = true;
	var $error_http_status_code = 400; // jaky HTTP kod se ma vytisknout v pripade, ze formular nebude validni...

	function pre_set_up(){
		// submit method GET is automatically set in IndexForm or DetailForm
		if(preg_match('/^(Index|Detail)Form$/i',get_class($this))){
			$this->set_method("get");
		}
	}

	function post_set_up(){
		$this->has_format_field && $this->add_format_field();

		// automaticky prevod labelu z "Article Id" na "article_id"...
		foreach($this->fields as $key => &$f){
			$f->label = $key;
		}
	}

	function add_format_field(){
		$this->add_field("format",new ChoiceField(array(
			"label" => "format",
			"initial" => "xml",
			"choices" => array(
				"json" => "json",
				"jsonp" => "jsonp",
				"xml" => "xml",
				"yaml" => "yaml",
			),
			"required" => !false, 
		)));
	}
}
