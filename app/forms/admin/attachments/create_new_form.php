<?php
class CreateNewForm extends AttachmentsForm{

	function set_up(){
		$this->add_field("attachment",new PupiqAttachmentField(array(
			"label" => _("File"),
		)));
		$this->add_name_field(array("required" => false));
	}
}
