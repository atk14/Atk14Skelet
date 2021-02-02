<?php
class AsyncFileUploadForm extends ApplicationForm {

	function set_up(){
		$this->add_field("name", new CharField(array(
			"label" => _("Name"),
			"max_length" => 255,
		)));

		$this->add_field("image", new AsyncFileField(array(
			"allowed_mime_types" => ["/^image\/.*/"],
		)))->update_messages([
			"disallowed_mime_type" => _("This is not an image"),
		]);

		$this->add_field("file2", new AsyncFileField(array(
			"required" => false,
		)));

		$this->add_field("file3", new FileField(array(
			"label" => _("Ordinary file input"),
			"required" => false,
		)));
	}

	function is_small(){
		return true;
	}
}
