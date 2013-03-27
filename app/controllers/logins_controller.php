<?php
class LoginsController extends ApplicationController{
	function create_new(){
		$this->page_title = _("Sign in");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(!$user = User::Login($d["login"],$d["password"])){
				$this->form->set_error(_("Bad username or password"));
				return;
			}

			$this->_login_user($user);

			$this->flash->success(sprintf(_("You have been successfuly logged in as <em>%s</em>"),h($user->getLogin())));
			$this->_redirect_to("main/index");
		}
	}

	function destroy(){
		if($this->request->post()){
			$this->session->clear("logged_user_id");
			$this->flash->success(_("You have been successfuly logged out"));
		}
		$this->_redirect_to("main/index");
	}
}
