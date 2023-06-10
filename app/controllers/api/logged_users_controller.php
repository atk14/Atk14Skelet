<?php
class LoggedUsersController extends ApiController{

	/**
	 * ### Provides information about currently logged user
	 *
	 * Returns empty array when user is not logged in.
	 */
	function detail(){
		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			$this->api_data = $this->_dump_logged_user();
		}
	}
}
