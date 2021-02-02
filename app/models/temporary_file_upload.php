<?php
definedef("TEMPORARY_FILE_UPLOADS_DIRECTORY",TEMP . "/temporary_file_uploads/");
definedef("TEMPORARY_FILE_UPLOADS_MAX_FILESIZE",512 * 1024 * 1024); // 512MB
definedef("TEMPORARY_FILE_UPLOADS_MAX_AGE", 2 * 60 * 60); // 2 hours

class TemporaryFileUpload extends ApplicationModel {

	var $_Name = "file";

	static function CreateNewRecordByHttpUploadedFile($file,$options = array()){
		$now = now();
		$values = array(
			"id" => self::GetNextId(),
			"created_at" => $now,

			"filename" => $file->getFileName(),
			"filesize" => $file->getTotalFileSize(),
			"mime_type" => $file->getMimeType(),

			"chunked_upload" => false,
		);

		if($file->chunkedUpload()){
			$values["chunked_upload"] = true;
			$values["bytes_uploaded"] = $file->getFileSize();
			$values["last_chunk_uploaded_at"] = $now;
		}

		$time = strtotime($values["created_at"]);
		$path = [
			date("Y-m-d",$time),
			date("H",$time),
			date("i",$time),
			$values["id"].".dat",
		];
		if(TEST){
			array_unshift($path,"test");
		}

		$path = join("/",$path);

		$full_path = TEMPORARY_FILE_UPLOADS_DIRECTORY . "/" . $path;
		Files::MkdirForFile($full_path,$err);

		$stat = $file->moveTo($full_path);
		myAssert($stat);

		$values["path"] = $path;

		return self::CreateNewRecord($values,$options);
	}

	function getFullPath(){ return TEMPORARY_FILE_UPLOADS_DIRECTORY . "/" . $this->getPath(); }

	function chunkedUpload(){ return $this->g("chunked_upload"); }

	function fullyUploaded(){
		return !$this->chunkedUpload() || $this->getFilesize()===$this->getBytesUploaded();
	}

	function appendChunk($file){
		myAssert($this->chunkedUpload());
		myAssert(!$this->fullyUploaded());
		myAssert($this->getFilesize()>=($this->getBytesUploaded() + $file->getFileSize()));

		$full_path = $this->getFullPath();
		myAssert(file_exists($full_path));

		myAssert((filesize($full_path)+$file->getFileSize())<=$this->getFilesize());

		$f = fopen($full_path,"ab");
		fwrite($f,$file->getContent(),$file->getFileSize());
		fclose($f);

		$this->s([
			"bytes_uploaded" => $this->getBytesUploaded() + $file->getFileSize(),
			"last_chunk_uploaded_at" => now(),
		]);
	}

	function getSuffix(){
		if(preg_match("/\.([^\.]+)$/",$this->getFilename(),$matches)){
			return strtolower($matches[1]);
		}
	}

	function destroy($destroy_for_real = null){
		if(file_exists($this->getFullPath())){
			Files::Unlink($this->getFullPath());
		}
		return parent::destroy();
	}

	// This method is called in widget AsyncFileInput
	function setFormName($name){
		$this->_Name = (string)$name;
	}

	// This method is called in TemporaryFileUpload_as_HTTPUploadedFile
	function getFormName(){
		return $this->_Name;
	}
}

class TemporaryFileUpload_as_HTTPUploadedFile extends HTTPUploadedFile{

	function __construct($temporary_file_upload){
		// TODO: This may lead to troubles in the future because the private stuff of HTTPUploadedFile is manipulated
		$this->_TmpFileName = $temporary_file_upload->getFullPath();
		$this->_Name = $temporary_file_upload->getFormName();
		$this->_FileName = $temporary_file_upload->getFilename();
		$this->_MimeType = $temporary_file_upload->getMimeType();
	}
}
