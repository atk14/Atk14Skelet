<?php
class EditForm extends UsersForm{
	function set_up(){
		$this->_add_basic_account_fields();

		$this->enable_csrf_protection();
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(!$this->has_errors() && $d==$this->get_initial()){
			$this->set_error(_("Nothing has been changed"));
		}
		
		return array($err,$d);
	}
}
