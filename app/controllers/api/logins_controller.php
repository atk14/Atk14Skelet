<?php
class LoginsController extends ApiController{
	var $api_root_element = "user"; // by default it was "login"

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

	/**
	 * ### Login user
	 *
	 * Upon a successful login an information about the user is returned.
	 *
	 * #### HTTP status codes
	 *
	 * * 401 Unauthorized: Bad password
	 * * 404 Not Found: There is no such user
	 * * 425 Too Early: Retry the next login attempt after the specific amount of time
	 * * 503 Service Unavailable
	 * * 201 Created
	 * * 400 Bad Request
	 */
	function create_new(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){

			//* // comment out this block to enable the sign-in procedure in the API
			if(PRODUCTION){
				$this->_report_fail(_("For security reasons, this function is disabled"),503);
				return;
			}
			//*

			if(InvalidLoginAttempt::IsRemoteAddressBlocked($this->request->getRemoteAddr(),$realease_time)){
				$this->_report_fail(InvalidLoginAttempt::BuildLoginAttemptDelayMessage($realease_time),425);
				return;
			}

			$user = User::Login($d["login"],$d["password"],$bad_password);
			if(!$user){
				InvalidLoginAttempt::CreateNewRecord([
					"login" => $d["login"],
				]);
				$this->logger->warn("invalid login attempt on $d[login] from ".$this->request->getRemoteAddr());

				if($bad_password){
					$this->_report_fail(_("Bad password"),401); // Unauthorized
					return;
				}
				$this->_report_fail(_("There is no such user"),404); // Not Found
				return;
			}
			$this->_login_user($user);
			$this->api_data = $this->_dump_user($user);
		}
	}

	/**
	 * ### Logout user
	 *
	 */
	function destroy(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->_logout_user();
			$this->api_data = $this->_dump_logged_user();
		}
	}
}
