<?php
class UsersController extends ApplicationController{
	function create_new(){
		$this->page_title = _("New user registration");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$user = User::CreateNewRecord($d);

			$this->_login_user($user);

			$this->flash->success(_("You have been successfuly registered and now you are logged in"));
			$this->_redirect_to_action("main/index");
		}
	}

}
