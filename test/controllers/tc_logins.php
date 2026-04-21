<?php
/**
 *
 * @fixture users
 */
class TcLogins extends TcBase{

	function test(){
		$client = $this->client;

		// no mention about the user on the front page
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertStringNotContains('rambo.tester',$client->getContent());

		// an invalid login
		$ctrl = $client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "badTry",
		));
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue($ctrl->form->has_errors());
		$this->assertStringContains('Wrong login and password combination',$client->getContent());

		// the correct login
		$ctrl = $client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "Secret123",
		));
		$this->assertEquals(303,$client->getStatusCode()); // redirection
		$this->assertFalse($ctrl->form->has_errors());

		// there is a mention about the user on the front page
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertStringContains('rambo.tester',$client->getContent());

		// logout
		$client->post("logins/destroy");
		$this->assertEquals(303,$client->getStatusCode());

		// there is no mention about the user on the front page
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertStringNotContains('rambo.tester',$client->getContent());

		// trying to login with the actual hash must not be successful
		$rocky = $this->users["rocky"];
		$this->assertEquals('$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta',$rocky->getPassword()); // see test/fixtures/users.yml

		$ctrl = $client->post("logins/create_new",array(
			"login" => "rocky",
			"password" => '$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta',
		));
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue($ctrl->form->has_errors());

		$ctrl = $client->post("logins/create_new",array(
			"login" => "rocky",
			"password" => "secret"
		));
		$this->assertEquals(303,$client->getStatusCode());
		$this->assertFalse($ctrl->form->has_errors());

		$ctrl = $client->get("main/index");
		$rocky = $ctrl->_get_logged_user();
		$this->assertTrue(!!$rocky);

		// deactivated user must not stay logged in
		$this->users["rocky"]->s([
			"active" => false,
			"updated_by_user_id" => null,
		]);
		$ctrl = $client->get("main/index");
		$rocky = $ctrl->_get_logged_user();
		$this->assertNull($rocky);
	}

	function test_open_redirect_prevention(){
		$client = $this->client;

		// absolute external URL in return_uri must not redirect off-site
		$client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "Secret123",
			"return_uri" => "https://evil.com/steal-credentials",
		));
		$this->assertEquals(303,$client->getStatusCode());
		$this->assertStringNotContains("evil.com",$client->getLocation());

		$client->post("logins/destroy");

		// protocol-relative URL must also be blocked
		$client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "Secret123",
			"return_uri" => "//evil.com",
		));
		$this->assertEquals(303,$client->getStatusCode());
		$this->assertStringNotContains("evil.com",$client->getLocation());

		$client->post("logins/destroy");

		// valid relative URI must still work
		$client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "Secret123",
			"return_uri" => "/en/articles/",
		));
		$this->assertEquals(303,$client->getStatusCode());
		$this->assertStringContains("/en/articles/",$client->getLocation());
	}
}
