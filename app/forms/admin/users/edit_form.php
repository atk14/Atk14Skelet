<?php
class EditForm extends UsersForm{
	function set_up(){
		parent::set_up();

		if($this->controller->user->getId()==1){
			// the default admin should be administrator forever
			$this->fields["is_admin"]->disabled = true;
		}
	}
}
