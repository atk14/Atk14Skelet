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

	function login_as_user(){
		if(!$this->request->post()){
			$this->_execute_action("error404");
			return;
		}
		$this->_login_user($this->user,array("fake_login" => true));
		$this->flash->success(sprintf(_("Now you are logged as <em>%s</em>"),h($this->user->getLogin())));
		$this->_redirect_to(array("namespace" => "", "action" => "main/index"));
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy","login_as_user"))){
			$this->_find("user");
		}
	}
}
