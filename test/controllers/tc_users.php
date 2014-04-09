<?php
class TcUsers extends TcBase{
	function test_create_new(){
		// we are visiting the front page
		$controller = $this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertNotContains("john.doe.tester",$this->client->getContent()); // the front page doesn't contain info about user john.doe.tester, actually this user doesn't exist yet
		$this->assertEquals(null,$controller->logged_user);

		// we are visiting the page for user registration
		$controller = $this->client->get("users/create_new");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertEquals(null,$controller->logged_user);

		// we are posting data to the page
		$controller = $this->client->post("users/create_new",array(
			"login" => "john.doe.tester",
			"name" => "John Doe",
			"email" => "john@doe.com",
			"password" => "no_more_fears",
			"password_repeat" => "no_more_fears",
		));

		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting...
		$this->assertContains('You have been successfuly registered',$controller->flash->success());

		// we are visiting the front page again
		$controller = $this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertContains("john.doe.tester",$this->client->getContent()); // now the page contains info about user john.doe.tester
		$this->assertEquals("john.doe.tester",$controller->logged_user->getLogin());
	}
}
