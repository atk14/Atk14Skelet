<?php
class UserField extends ObjectField {

	function __construct($options = array()){
		parent::__construct($options);
		$this->update_messages(array(
			"not_found" => _("Takový uživatel neexistuje"),
		));
	}
}
