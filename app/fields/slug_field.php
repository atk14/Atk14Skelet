<?php
class SlugField extends RegexField{
	function __construct($options = array()){
		$options += array(
			"label" => _("Slug"),
			"max_length" => 64,
			"hint" => _("funny-green-mug"),
		);
		parent::__construct('/^[a-z](|-?[a-z0-9]+)*$/',$options);

		$this->update_messages(array(
			"invalid" => _("Write something like black-cat-white-cat")
		));
	}
}
