<?php
class LoginsController extends ApplicationController{
	function create_new(){
		if($this->logged_user){ return $this->_redirect_to("destroy"); }

		$this->page_title = _("Sign in");

		if($this->request->get()){ $this->form->set_initial($this->params); }

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(!$user = User::Login($d["login"],$d["password"])){
				$this->logger->warn("bad login attempt on $d[login] from ".$this->request->getRemoteAddr());

				if(User::FindByLogin($d["login"])){
					$this->form->set_error(sprintf(_('Wrong login and password combination. <a href="%s">Have you forgotten your password?</a>'),$this->_link_to(array("action" => "password_recoveries/create_new", "login" => $d["login"]))));
				}else{
					$this->form->set_error(_('Wrong login and password combination'));
				}
				return;
			}

			$this->_login_user($user);

			$this->flash->success(sprintf(_("You have been successfuly logged in as <em>%s</em>"),h($user->getLogin())));
			$this->_redirect_after_login_or_logout();
		}
	}

	function destroy(){
		if(!$this->logged_user){ return $this->_redirect_to("create_new"); }

		$this->page_title = _("Sign out");
		if($this->logged_user){
			if($this->request->post()){
				$this->_logout_user();
				$this->flash->success(_("You have been successfuly logged out"));
				$this->_redirect_after_login_or_logout();
			}
		}else{
			$this->_redirect_after_login_or_logout();
		}
	}

	function _redirect_after_login_or_logout(){
		if($uri = $this->params->getString("return_uri")){
			return $this->_redirect_to($uri);
		}
		return $this->_redirect_to("main/index");
	}
}
