<?php
class AttachmentsForm extends AdminForm{

	function set_up($options = array()){
		$this->add_field("url",new AsyncPupiqAttachmentField(array(
			"label" => _("File"),
		)));
		$options += array(
			"label" => _("Title"),
			"hint" => _("User Manual"),
			"max_length" => 255,
			"required" => false,
		);
		$this->add_translatable_field("name",new CharField($options));
	}
}
