<?php
class UsersController extends ApiController{

	/**
	 * Provides information about currently logged user
	 *
	 * Returns empty array when user is not logged in.
	 */
	function detail(){
		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			if($u = $this->_get_logged_user()){
				$this->api_data = array(	
					"id" => $u->getId(),
					"login" => $u->getLogin(),
					"name" => $u->getName(),
					"email" => $u->getEmail(),
					"is_admin" => $u->isAdmin()
				);
			}else{
				$this->api_data = array();
			}
		}
	}
}
