<?php
class SlugField extends RegexField{

	function __construct($options = array()){
		$options += array(
			"label" => _("Slug"),
			"max_length" => 64,
			"hint" => _("funny-green-mug"),
			"auto_slugify" => true,
		);

		$this->auto_slugify = $options["auto_slugify"];
		unset($options["auto_slugify"]);

		parent::__construct('/^[a-z0-9](|-?[a-z0-9]+)*$/',$options);

		$this->update_messages(array(
			"invalid" => _("Write something like black-cat-white-cat")
		));
	}

	function clean($value){
		if($this->auto_slugify){
			$value = String4::ToObject($value)->toSlug()->toString();
		}
		return parent::clean($value);
	}
}
