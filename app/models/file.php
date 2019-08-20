<?php
// jako Attachment, ale toto je do textu vkladany objekt (Iobject)
class File extends Iobject {

	use TraitPupiqAttachment;

	function getTitle(){
		$title = parent::getTitle();
		return $title ? $title : $this->getFilename();
	}

	// pro kompatibilitu s Attachment
	function getName(){
		return $this->getTitle();
	}

}
