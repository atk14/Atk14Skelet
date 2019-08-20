<?php
class CreateNewForm extends PagesForm {

	function clean(){
		list($err,$values) = parent::clean();

		if(isset($values["code"]) && ($p = Page::FindByCode($values["code"]))){
			$this->set_error("code",_("The same code is used on a different page"));
		}

		return array($err,$values);
	}
}
