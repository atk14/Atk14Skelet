<?php
class UsersController extends AdminController{
	function index(){
		$this->page_title = _("Users list");

		$this->sorting->add("created_at",array("reverse" => "true"));
		$this->sorting->add("updated_at","COALESCE(updated_at,'2000-01-01') DESC, created_at DESC, id DESC","COALESCE(updated_at,'2099-01-01'), created_at, id");
		$this->sorting->add("is_admin","is_admin DESC, LOWER(login)","is_admin ASC, LOWER(login)");
		$this->sorting->add("login","LOWER(login)");
		$this->sorting->add("id");
		$this->sorting->add("name","LOWER(name)");
		$this->sorting->add("email","COALESCE(LOWER(name),'')");

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

	function create_new(){
		$this->page_title = _("Create a new user");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			User::CreateNewRecord($d);
			$this->flash->success(_("The user has been created successfully"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$this->page_title = sprintf(_("Editing user %s"),h($this->user));

		$this->form->set_initial($this->user);
		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if($d==$this->form->get_initial()){
				$this->flash->notice(_("Nothing has been changed"));
				return $this->_redirect_back();
			}

			$d["updated_by_user_id"] = $this->logged_user;
			$this->user->s($d);
			$this->flash->success(_("The user entry has been updated"));
			$this->_redirect_back();
		}
	}

	function edit_password(){
		$this->page_title = sprintf(_("Setting a new password to the user %s"),h($this->user));

		$this->form->set_initial("password",String4::RandomPassword(10));

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["updated_by_user_id"] = $this->logged_user;
			$this->user->s($d);
			$this->flash->success(strtr(_('The new password has been set to the user <em>%user%</em>.<br>Would be nice to let him know at email address <a href="mailto:%email%">%email%</a>.'),array(
				"%user%" => h($this->user),
				"%email%" => h($this->user->getEmail()
			))));
			$this->logger->info("administrator $this->logged_user just set new password to user $this->user (#".$this->user->getId().")");
			$this->_redirect_back();
		}
	}

	function destroy(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }
		if(!$this->user->isDeletable() || $this->user->getId()==$this->logged_user->getId()){ return $this->_execute_action("error403"); }

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
		if(in_array($this->action,array("edit","edit_password","destroy","login_as_user"))){
			$this->_find("user");
		}
	}
}
