<?php
class FilesForm extends AdminForm{
	function set_up(){
		$this->add_field("url", new PupiqAttachmentField(array(
			"label" => _("Soubor"),
		)));
		$this->add_title_field(["label" => _("Název")]);
	}
}
