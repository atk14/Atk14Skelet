<?php
class TcApplication extends TcBase{
	function test_access(){
		$client = $this->client;

		// access to administration is forbidden for non logged user
		$this->_logoutUser();
		$client->get("main/index");
		$this->assertEquals(403,$client->getStatusCode());

		// an ordinary user can't access administration
		$user = User::CreateNewRecord(array(
			"login" => "ordinary_testing_user",
			"password" => "x",
		));
		$this->_loginUser($user);
		$client->get("main/index");
		$this->assertEquals(403,$client->getStatusCode());
		$this->assertContains("ordinary_testing_user",$client->getContent()); // on the page there must be the user mentioned

		// only administrator can access administration
		$this->_loginUser(1); // admin
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
	}
}
