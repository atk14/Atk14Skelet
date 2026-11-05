<?php
class TcLoggedUsers extends TcBase{

	function test(){
		// logged user info - user is not logged in
		$user = $this->_get("logged_users/detail");
		$this->assertEquals(array(),$user);

		// log in through the API
		$this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "admin",
		),$status_code);
		$this->assertEquals(201,$status_code);

		// logged user info - user is logged in
		$user = $this->_get("logged_users/detail",array(),$status_code);
		$this->assertEquals(200,$status_code);
		$this->assertEquals("admin",$user["login"]);
		$this->assertEquals(1,$user["id"]);
		$this->assertEquals(true,$user["is_admin"]);

		// log out
		$this->_post("logins/destroy");

		// logged user info - user is logged out again
		$user = $this->_get("logged_users/detail");
		$this->assertEquals(array(),$user);
	}
}
