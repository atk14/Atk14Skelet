<?php
class FilesForm extends AdminForm{

	function set_up(){
		$this->add_field("url", new AsyncPupiqAttachmentField(array(
			"label" => _("File"),
		)));
		$this->add_title_field(["label" => _("Title")]);
	}
}
