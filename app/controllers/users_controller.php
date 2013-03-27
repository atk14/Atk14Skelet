<?php
class UsersController extends ApplicationController{
	function create_new(){
		$this->page_title = _("New user registration");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$user = User::CreateNewRecord($d);
			$this->logger->info("user $user just registered from ".$this->request->getRemoteAddr());

			$this->_login_user($user);

			$this->flash->success(sprintf(_("You have been successfuly registered and now you are logged in as <em>%s</em>"),h("$user")));
			$this->_redirect_to_action("main/index");
		}
	}
}
