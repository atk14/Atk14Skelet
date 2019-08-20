<?php
class EditForm extends PagesForm {

	function set_up() {
		parent::set_up();
		$this->add_slug_field();
	}

	function clean(){
		list($err,$values) = parent::clean();

		if(isset($values["code"]) && ($p = Page::FindFirst("code=:code AND id!=:id",array(":code" => $values["code"], ":id" => $this->controller->page)))){
			$this->set_error("code",_("The same code is used on a different page"));
		}

		return array($err,$values);
	}
}
