<?php
class IndexForm extends ApiForm{

	function set_up(){
		$this->add_field("table_name", new RegexField('/^[a-z0-9_]+$/',array(
			"max_length" => 100,
			"help_text" => _('Table of the record, e.g. "pages"'),
		)));

		$this->add_field("record_id", new IntegerField(array(
			"min_value" => 1,
			"help_text" => _("ID of the record"),
		)));
	}
}
