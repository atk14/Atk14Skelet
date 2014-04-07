<?php
class CreateNewForm extends AdminForm{
	function set_up(){
		$this->add_field("title",new CharField(array(
			"label" => _("Title"),
			"max_length" => 255,
		)));

		$this->add_field("body",new TextField(array(
			"label" => _("Body"),
			"help_text" => _("Mardown format is expected"),
		)));

		$this->add_field("published_at",new DateTimeField(array(
			"label" => _("Published At"),
			"initial" => time(),
		)));
	}
}
