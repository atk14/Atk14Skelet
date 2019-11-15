<?php
class EditForm extends UsersForm{

	function set_up(){
		parent::set_up();

		if($this->controller->user->getId()==User::ID_SUPERADMIN){
			// the default admin should not be deactivated and should stay as administrator forever
			$this->fields["active"]->disabled = true;
			$this->fields["is_admin"]->disabled = true;
		}
	}
}
