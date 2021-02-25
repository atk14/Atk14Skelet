<?php
class EditForm extends ArticlesForm{

	function set_up(){
		parent::set_up();
		$this->add_slug_field();
	}

	function clean(){
		list($err,$data) = parent::clean();

		$this->_clean_slugs($data);

		return array($err,$data);
	}
}
