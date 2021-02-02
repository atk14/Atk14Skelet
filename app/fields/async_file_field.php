<?php
class AsyncFileField extends FileField {

	function __construct($options = array()){
		$options += [
			"widget" => new AsyncFileInput(),
		];

		parent::__construct($options);

		$this->update_messages(array(
			"temporary_file_not_found" => _("The file has been already deleted on the server, please upload it again"),
			"temporary_file_not_fully_uploaded" => _("The file was now fully upload, please upload it again"),
		));
	}

	function clean($value){
		if(is_string($value) && !is_numeric($value)){
			$value = TemporaryFileUpload::GetInstanceByToken($value);
			if(!$value){
				return array($this->messages["temporary_file_not_found"],null);
			}
		}
		if(is_a($value,"TemporaryFileUpload")){
			$file = $value;
			if(!file_exists($file->getFullPath())){
				return array($this->messages["temporary_file_not_found"],null);
			}
			if(!$file->fullyUploaded()){
				return array($this->messages["temporary_file_not_fully_uploaded"],null);
			}
			$value = new TemporaryFileUpload_as_HTTPUploadedFile($file);
		}

		return parent::clean($value);
	}
}
