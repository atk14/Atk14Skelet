<?php
class UsersController extends AdminController{
	function index(){
		$this->page_title = _("Users list");

		$this->sorting->add("created_at",array("reverse" => "true"));
		$this->sorting->add("updated_at",array("reverse" => "true"));
		$this->sorting->add("is_admin","is_admin DESC, UPPER(login)","is_admin ASC, UPPER(login)");
		$this->sorting->add("login","UPPER(login)");

		$this->tpl_data["finder"] = User::Finder(array(
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}

	function edit(){
		$this->page_title = sprintf(_("Editing user %s"),h($this->user));

		$this->form->set_initial($this->user);
		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->user->s($d);
			$this->flash->success("The user entry has been updated");
			$this->_redirect_back();
		}
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("user");
		}
	}
}
