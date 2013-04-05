<?php
class LoginsController extends ApplicationController{
	function create_new(){
		$this->page_title = _("Sign in");

		if($this->request->get()){ $this->form->set_initial($this->params); }

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(!$user = User::Login($d["login"],$d["password"])){
				$this->logger->warn("bad login attempt on $d[login] from ".$this->request->getRemoteAddr());
				$this->form->set_error(_("Bad username or password"));
				return;
			}

			$this->_login_user($user);
			$this->logger->info("user $user just logged in from ".$this->request->getRemoteAddr());

			$this->flash->success(sprintf(_("You have been successfuly logged in as <em>%s</em>"),h($user->getLogin())));
			$this->_redirect_to("main/index");
		}
	}

	function destroy(){
		if($this->request->post() && $this->logged_user){
			$this->logger->info("user $this->logged_user logged out from ".$this->request->getRemoteAddr());
			$this->_logout_user();
			$this->flash->success(_("You have been successfuly logged out"));
		}
		$this->_redirect_to("main/index");
	}
}
