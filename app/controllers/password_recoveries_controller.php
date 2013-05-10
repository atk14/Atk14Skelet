<?php
class PasswordRecoveriesController extends ApplicationController{

	function create_new(){
		if($this->request->get()){ $this->form->set_initial($this->params); }
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			($user = User::FindByLogin($d["login"])) ||
			($user = User::FindByEmail($d["login"],array("order_by" => "created_at DESC")));
			if(!$user){
				$this->form->set_error("login",_("There is not such user with the given login or e-mail"));
				return;
			}
			if($user->getId()==1){
				$this->form->set_error("Sorry, there is absolutely no chance to reset admins password");
				return;
			}

			$password_recovery = PasswordRecovery::CreateNewRecord(array(
				"user_id" => $user,
				"email" => $user->getEmail(),
				"created_from_addr" => $this->request->getRemoteAddr(),
			));

			$this->mailer->execute("notify_password_recovery",array(
				"password_recovery" => $password_recovery,
			));

			$this->_redirect_to_action("sent");
		}
	}


	function recovery(){
		if(!$password_recovery = $this->tpl_data["password_recovery"] = PasswordRecovery::GetInstanceByToken($this->params->getString("token"))){
			$this->_execute_action("invalid_url");
			return;
		}

		if(!$password_recovery->isActive()){
			$this->_execute_action("expired_recovery");
			return;
		}

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$user = $password_recovery->getUser();
			$user->s("password",$d["password"]);

			$password_recovery->s(array(
				"recovered_at" => date("Y-m-d H:i:s"),
				"recovered_from_addr" => $this->request->getRemoteAddr(),
			));

			$this->flash->success(_("Your password has been updated successfuly"));
			$this->_redirect_to_action("logins/create_new",array("login" => $user->getLogin()));
		}
	}

	function sent(){ }

	function invalid_url(){ }

	function expired_recovery(){ }

	function _before_filter(){
		$this->page_title = _("Password recovery");
	}
}
