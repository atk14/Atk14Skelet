<?php
class ExtendedPasswordFieldSecondForm extends ExtendedPasswordFieldForm {

	function set_up(){
		parent::set_up();
		$this->set_prefix("second");
	}
}
