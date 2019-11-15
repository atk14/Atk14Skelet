<?php
// Traita pro Attachment a File
trait TraitPupiqAttachment {

	static function CreateNewRecord($values,$options = array()){
		$pa = new PupiqAttachment($values["url"]);
		$values += array(
			"filename" => $pa->getFilename(),
			"filesize" => $pa->getFilesize(),
			"mime_type" => $pa->getMimeType(),
		);
		return parent::CreateNewRecord($values,$options);
	}

	function setValues($values,$options = array()){
		if(in_array("url",array_keys($values))){
			$pa = new PupiqAttachment($values["url"]);
			$values += array(
				"filename" => $pa->getFilename(),
				"filesize" => $pa->getFilesize(),
				"mime_type" => $pa->getMimeType(),
			);
		}
		return parent::setValues($values,$options);
	}

	function getUrl($pupiq_url = false){
		global $ATK14_GLOBAL;

		if($pupiq_url){
			return $this->_getPupiqAttachment()->getUrl(); // tady je transparentni prevod na URL s PUPIQ_PROXY_HOSTNAME
		}

		$controller = String4::ToObject(get_class($this))->pluralize()->underscore()->toString(); // "Attachment" -> "attachments"

		return Atk14Url::BuildLink(array(
			"namespace" => "",
			"controller" => $controller,
			"lang" => $ATK14_GLOBAL->getDefaultLang(),
			"action" => "detail",
			"token" => $this->getToken(array("hash_length" => 10, "extra_salt" => "detail")),
			"filename" => $this->getFilename(),
		),array(
			"with_hostname" => true,
			"connector" => "&",
		));
	}

	function getFilename(){
		return $this->_getPupiqAttachment()->getFilename();
	}

	function getSuffix(){
		return $this->_getPupiqAttachment()->getSuffix();
	}

	function _getPupiqAttachment(){
		return new PupiqAttachment($this->g("url"));
	}
}
