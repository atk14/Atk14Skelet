<?php
class EditForm extends ArticlesForm{

	function set_up(){
		parent::set_up();
		$this->add_slug_field();
	}
}
