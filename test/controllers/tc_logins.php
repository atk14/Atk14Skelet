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
		$this->assertNotContains('rambo.tester',$client->getContent());

		// an invalid login
		$ctrl = $client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "badTry",
		));
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue($ctrl->form->has_errors());
		$this->assertContains('Wrong login and password combination',$client->getContent());

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
		$this->assertContains('rambo.tester',$client->getContent());

		// logout
		$client->post("logins/destroy");
		$this->assertEquals(303,$client->getStatusCode());

		// there is no mention about the user on the front page
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertNotContains('rambo.tester',$client->getContent());

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
	}
}
