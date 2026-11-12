<?php
class TcLogins extends TcBase{
	function test(){
		// logged user info - user is not logged in
		$user = $this->_get("logins/detail");
		$this->assertEquals(array(),$user);

		// bad login
		$this->_post("logins/create_new",array(
			"login" => "ugly.boy",
			"password" => "darkWING",
		),$status_code);
		$this->assertEquals(404,$status_code);

		// bad password
		$this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "darkWING",
		),$status_code);
		$this->assertEquals(401,$status_code);

		// success
		$user = $this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "admin",
		),$status_code);
		$this->assertEquals(201,$status_code);
		$this->assertEquals("admin",$user["login"]);
		$this->assertEquals(1,$user["id"]);

		// logged user info - user is logged in
		$user = $this->_get("logins/detail");
		$this->assertEquals("admin",$user["login"]);
		$this->assertEquals(1,$user["id"]);
	}

	function test_login_throttling_after_repeated_invalid_attempts(){
		// make sure we start from a logged-out state (test methods share the same client/session)
		$this->_post("logins/destroy");

		$bad_credentials = array(
			"login" => "admin",
			"password" => "darkWING",
		);

		// MAX_INVALID_LOGIN_ATTEMPTS (default 5) failed attempts are let through normally
		for($i = 0; $i < 5; $i++){
			$data = $this->_post("logins/create_new",$bad_credentials,$status_code);
			$this->assertEquals(401,$status_code);
			$this->assertEquals(array("Bad password"),$data);
		}

		// the next attempt gets throttled, even with the correct password
		$data = $this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "admin",
		),$status_code);
		$this->assertEquals(425,$status_code);
		$this->assertStringContains("Delay the form submission for",$data[0]);

		// the user did not get logged in
		$user = $this->_get("logins/detail");
		$this->assertEquals(array(),$user);
	}
}
