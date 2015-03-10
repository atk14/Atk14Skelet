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
}
