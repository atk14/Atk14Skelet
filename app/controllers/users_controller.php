<?php
class UsersController extends ApplicationController{
	function detail(){
		$this->page_title = _("User profile");
	}

	function create_new(){
		if($this->logged_user){
			$this->flash->warning(_("To register as a new user, sign out first"));
			$this->_redirect_to(array(
				"controller" => "logins",
				"action" => "destroy",
				"return_uri" => $this->request->getUri(),
			));
			return;
		}
		$this->page_title = _("New user registration");

		$this->tpl_data["js_validator"] = $jv = $this->form->js_validator();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["registered_from_ip_addr"] = $this->request->getRemoteAddr();
			$user = User::CreateNewRecord($d);
			$this->logger->info("user $user just registered from ".$this->request->getRemoteAddr());

			$this->_login_user($user);

			$this->flash->success(sprintf(_("You have been successfuly registered and now you are logged in as <em>%s</em>"),h("$user")));
			$this->_redirect_to_action("main/index");
		}
	}

	function edit(){
		$this->page_title = _("Change your account data");

		$this->form->set_initial($this->logged_user);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->logged_user->s($d);
			$this->flash->success(_("Your account data has been updated"));
			$this->_redirect_to_action("detail");
		}
	}

	function edit_password(){
		$this->page_title = _("Change your password");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(!$this->logged_user->isPasswordCorrect($d["current_password"])){
				$this->form->set_error("current_password",_("This is not your current password"));
				return;
			}
			$this->logged_user->s("password",$d["password"]);
			$this->flash->success(_("Your password has been updated successfuly"));
			$this->_redirect_to_action("detail");
		}
	}

	function _before_filter(){
		if(in_array($this->action,array("detail","edit","edit_password"))){
			if(!$this->logged_user){
				$this->_execute_action("error403");
				return;
			}
		}
	}
}
