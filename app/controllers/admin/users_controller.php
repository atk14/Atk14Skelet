<?php
class UsersController extends ApplicationController{
	function index(){
		$this->page_title = _("Users list");

		$this->sorting->add("created_at",array("reverse" => "true"));
		$this->sorting->add("id");
		$this->sorting->add("login","UPPER(login)");

		$this->tpl_data["finder"] = User::Finder(array(
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}

	function edit(){
		$this->page_title = sprintf(_("Editing user #%s"),$this->user->getId());
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("user");
		}
	}
}
