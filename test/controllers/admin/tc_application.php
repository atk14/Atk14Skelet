<?php
/**
 *
 * @fixture users
 */
class TcApplication extends TcBase{

	function test_access(){
		$client = $this->client;

		// access to administration is forbidden for non logged user
		$this->_logoutUser();

		// on the welcome page there is a transparent redirection to the login form...
		$client->get("main/index");
		$this->assertEquals(302,$client->getStatusCode()); // redirect to the login form
		$new_location_params = Atk14Url::RecognizeRoute($client->getLocation());
		$this->assertEquals("",$new_location_params["namespace"]);
		$this->assertEquals("logins",$new_location_params["controller"]);
		$this->assertEquals("create_new",$new_location_params["action"]);

		// on another page there is no redirection
		$client->get("articles/index");
		$this->assertEquals(403,$client->getStatusCode()); // redirect to the login form

		// an ordinary user can't access administration
		$user = $this->users["rambo_tester"];
		$this->_loginUser($user);
		$client->get("main/index");
		$this->assertEquals(403,$client->getStatusCode());
		$this->assertContains("rambo.tester",$client->getContent()); // on the page there must be the user mentioned

		// only administrator can access administration
		$this->_loginUser(1); // admin
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
	}
}
