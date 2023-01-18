<?php
class UsersController extends ApplicationController{

	function detail(){
		$this->page_title = _("User profile");
	}

	function create_new(){
		if(defined("USER_REGISTRATION_ENABLED") && !constant("USER_REGISTRATION_ENABLED")){
			return $this->_execute_action("error404");
		}

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

		$this->head_tags->setCanonical($this->_build_canonical_url());

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["registered_from_ip_addr"] = $this->request->getRemoteAddr();
			$d["password"] = MyBlowfish::GetHash($d["password"]); // Make sure to encrypt password which even looks like a proper blowfish hash! :)
			$user = User::CreateNewRecord($d);
			$this->logger->info("user $user just registered from ".$this->request->getRemoteAddr());

			$this->mailer->notify_user_registration($user);

			$this->_login_user($user);

			if($uri = $this->params->getString("return_uri")){
				$this->flash->success(sprintf(_("You have been successfully registered and now you are logged in as <em>%s</em>"),h("$user")));
				return $this->_redirect_to($uri);
			}

			$this->_redirect_to("created");
		}
	}

	function created(){
		$this->page_title = _("Thank you for your registration!");
		$this->head_tags->setMetaTag("robots", "noindex,noarchive");
		$this->head_tags->setMetaTag("googlebot", "noindex");
	}

	function edit(){
		$this->page_title = $this->breadcrumbs[] = _("Change your account data");

		$this->form->set_initial($this->logged_user);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if($d==$this->form->get_initial()){
				$this->flash->notice(_("Nothing has been changed"));
				return $this->_redirect_to("detail");
			}

			$login_changed = false;
			if($this->logged_user->getEmail()===$this->logged_user->getLogin() && $this->logged_user->getEmail()!==$d["email"]){
				if(User::FindFirst("login",$d["email"])){
					$this->form->set_error("email",_("This email address is used by another user."));
					return;
				}
				$d["login"] = $d["email"];
				$login_changed = true;
			}
			$d["updated_by_user_id"] = $this->logged_user;
			$this->logged_user->s($d);

			$this->flash->success($login_changed ? sprintf(_("Your account data has been updated. Next time you should sign-in with login name <em>%s</em>."),h($d["email"])) : _("Your account data has been updated"));
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
				"password_changed_at" => now(),
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
				$this->_add_user_detail_breadcrumb();
			}
		}
	}
}
