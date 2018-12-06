<?php
class UsersController extends ApplicationController{
	function detail(){
		$this->page_title = _("User profile");
	}

	function create_new(){
		$form2 = $this->_get_form("CreateNewForm");
		$form2->set_prefix("form2");
		$form2->set_attr(["id" => "form2_users_create_new"]);
		$this->tpl_data["form2"] = $form2;

		if($this->logged_user){
			$this->flash->warning(_("To register as a new user, sign out first"));
			$this->_redirect_to(array(
				"controller" => "logins",
				"action" => "destroy",
				"return_uri" => $this->request->getUri(),
			));
			return;
		}
		$this->page_title = $this->breadcrumbs[] = _("New user registration");

		//$this->tpl_data["js_validator"] = $jv = $this->form->js_validator();

		if($this->request->post() && (($d = $this->form->validate($this->params)) || ($d = $form2->validate($this->params)))){
			$d["registered_from_ip_addr"] = $this->request->getRemoteAddr();
			$d["password"] = MyBlowfish::GetHash($d["password"]); // Make sure to encrypt password which even looks like a proper blowfish hash! :)
			$user = User::CreateNewRecord($d);
			$this->logger->info("user $user just registered from ".$this->request->getRemoteAddr());

			$this->mailer->notify_user_registration($user);

			$this->_login_user($user);

			$this->flash->success(sprintf(_("You have been successfully registered and now you are logged in as <em>%s</em>"),h("$user")));
			$this->_redirect_to("main/index");
		}
	}

	function edit(){
		$this->page_title = $this->breadcrumbs[] = _("Change your account data");

		$this->form->set_initial($this->logged_user);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if($d==$this->form->get_initial()){
				$this->flash->notice(_("Nothing has been changed"));
				return $this->_redirect_to("detail");
			}
			$d["updated_by_user_id"] = $this->logged_user;
			$this->logged_user->s($d);
			$this->flash->success(_("Your account data has been updated"));
			$this->_redirect_to("detail");
		}
	}

	function edit_password(){
		$this->page_title = $this->breadcrumbs[] = _("Change your password");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(!$this->logged_user->isPasswordCorrect($d["current_password"])){
				$this->form->set_error("current_password",_("This is not your current password"));
				return;
			}

			$d["password"] = MyBlowfish::GetHash($d["password"]); // Make sure to encrypt password which even looks like a proper blowfish hash! :)

			$this->logged_user->s(array(
				"password" => $d["password"],
				"updated_by_user_id" => $this->logged_user
			));
			$this->flash->success(_("Your password has been updated successfully"));
			$this->_redirect_to("detail");
		}
	}

	function _before_filter(){
		if(in_array($this->action,array("detail","edit","edit_password"))){
			if(!$this->logged_user){
				$this->_execute_action("error403");
				return;
			}

			if(preg_match('/^(edit|detail)/',$this->action)){
				$this->breadcrumbs[] = array(_("User profile"),$this->_link_to("detail"));
			}
		}
	}
}
