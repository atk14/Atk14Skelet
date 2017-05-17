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
		$client->post("logins/create_new",array(
			"login" => "rambo.tester",
			"password" => "Secret123",
		));
		$this->assertEquals(303,$client->getStatusCode()); // redirection

		// there is a mention about the user on the front page
		$client->get("main/index");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertContains('rambo.tester',$client->getContent());
	}
}
