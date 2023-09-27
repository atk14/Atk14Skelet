<?php
class PasswordRecoveriesController extends ApplicationController{

	function create_new(){
		if($this->request->get()){ $this->form->set_initial($this->params); }
		$this->head_tags->setMetaTag("robots", "noindex,noarchive");
		$this->head_tags->setCanonical($this->_build_canonical_url());

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			($user = User::FindByLogin($d["login"])) ||
			($user = User::FindByEmail($d["login"],array("order_by" => "created_at DESC")));
			if(!$user){
				$this->form->set_error("login",_("There is no such user with the given login or email"));
				return;
			}
			if(!$user->isActive()){
				$this->form->set_error("login",_("This user account has been deactivated. Password recovery process can not be started."));
				return;
			}
			if(!$user->getEmail()){
				$this->form->set_error("login",_("Password recovery can not be initiated for this user. Email address is not set."));
				return;
			}
			// To prevent password recovery for User#1, uncomment the following check
			//if($user->getId()==1){
			//	$this->form->set_error(_("To reset admins password use console like ATK14 ninja: $ ./scripts/migrate -f 0002_reset_admins_password_migration.php"));
			//	return;
			//}

			$password_recovery = PasswordRecovery::CreateNewRecord(array(
				"user_id" => $user,
				"email" => $user->getEmail(),
				"created_from_addr" => $this->request->getRemoteAddr(),
			));

			$this->mailer->notify_password_recovery($password_recovery);

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
			$user->s(array(
				"password" => $d["password"],
				"password_changed_at" => now(),
			));

			$password_recovery->s(array(
				"recovered_at" => now(),
				"recovered_from_addr" => $this->request->getRemoteAddr(),
			));

			$this->mailer->notify_password_update_in_recovery($password_recovery);

			$this->flash->success(_("Your password has been updated successfully"));
			$this->_redirect_to_action("logins/create_new",array("login" => $user->getLogin()));
		}
	}

	function sent(){ }

	function invalid_url(){
		$this->response->setStatusCode(404);
	}

	function expired_recovery(){
		$this->response->setStatusCode(404);
	}

	function _before_filter(){
		$this->breadcrumbs[] = array(_("Sign in"),$this->_link_to("logins/create_new"));
		$this->page_title = $this->breadcrumbs[] = _("Password recovery");
	}
}
