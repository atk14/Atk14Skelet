<?php
class Attachment extends LinkedObject implements Translatable{

	use TraitPupiqAttachment;

	static function GetTranslatableFields(){ return array("name"); }

	/**
	 * $attachments = Attachment::GetAttachments($article);
	 * $attachments = Attachment::GetAttachments($card,"reviews"); // "reviews" is a section name
	 */
	static function GetAttachments($obj,$options = array()){
		return Attachment::GetInstancesFor($obj,$options);
	}

	function getName($lang = null){
		if($name = parent::getName($lang)){
			return $name;
		}
		return $this->getFilename();
	}

	function getSuffix(){
		return $this->_getPupiqAttachment()->getSuffix();
	}

	static function AddAttachment($obj,$values,$options = array()){
		if(is_string($values)){
			$values = array("url" => $values);
		}
		return Attachment::CreateNewFor($obj,$values,$options);
	}
}
