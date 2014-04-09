<?php
class CreateNewForm extends TagsForm{
	function clean(){
		list($err,$d) = parent::clean();

		if(isset($d["tag"]) && Tag::FindFirstByTag($d["tag"])){
			$this->set_error("tag",_("The same tag already exists"));
		}

		return array($err,$d);
	}
}
