<?php
class LoginAvailabilitiesController extends ApiController{

	/**
	 * Checks out whether a given login is available for registration or has been already taken
	 */
	function detail(){
		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			$this->api_data = array("status" => User::FindByLogin($d["login"]) ? "taken" : "available");
		}
	}
}
