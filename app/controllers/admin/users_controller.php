<?php
class UsersController extends AdminController{
	function index(){
		$this->page_title = _("Users list");

		$this->sorting->add("created_at",array("reverse" => "true"));
		$this->sorting->add("updated_at",array("reverse" => "true"));
		$this->sorting->add("is_admin","is_admin DESC, UPPER(login)","is_admin ASC, UPPER(login)");
		$this->sorting->add("login","UPPER(login)");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$conditions[] = "UPPER(id||' '||login||' '||COALESCE(name,'')||' '||COALESCE(email,'')) LIKE UPPER('%'||:search||'%')";
			$bind_ar[":search"] = $d["search"];
		}

		$this->tpl_data["finder"] = User::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
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

	function destroy(){
		if(!$this->request->post() || !$this->user->isDeletable() || $this->user->getId()==$this->logged_user->getId()){ return $this->_execute_action("error404"); }

		$this->user->destroy();

		if(!$this->request->xhr()){
			$this->flash->success(_("The user entry has been deleted"));
			$this->_redirect_to("index");
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
