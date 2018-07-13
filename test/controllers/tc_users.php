<?php
/**
 *
 * @fixture users
 */
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

		// passwords don't match to each other
		$controller = $this->client->post("users/create_new",array(
			"login" => "john.doe.tester",
			"name" => "John Doe",
			"email" => "john@doe.com",
			"password" => "no_more_fears",
			"password_repeat" => "no_more_feras",
		));
		$this->assertEquals(true,$controller->form->has_errors());
		$this->assertEquals(200,$this->client->getStatusCode());

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
		$this->assertContains('You have been successfully registered',(string)$controller->flash->success());

		// testing outgoing email
		$mailer = $controller->mailer;
		$this->assertEquals('john@doe.com',$mailer->to);
		$this->assertContains('Thanks for signing up',$mailer->body);
		$this->assertContains('login: john.doe.tester',$mailer->body);

		// we are visiting the front page again
		$controller = $this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertContains("john.doe.tester",$this->client->getContent()); // now the page contains info about user john.doe.tester
		$this->assertEquals("john.doe.tester",$controller->logged_user->getLogin());

		// the new user has hashed password
		$john = User::FindByLogin("john.doe.tester");
		$this->assertNotEquals("no_more_fears",$john->getPassword());
		$this->assertTrue(MyBlowfish::IsHash($john->getPassword()));
	}

	function test_user_gives_a_proper_hash_as_password(){
		// we are posting data to the page
		$controller = $this->client->post("users/create_new",array(
			"login" => "john.doe.joker",
			"name" => "John Doe",
			"email" => "john@doe.com",
			"password" => '$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta', // hash for secret
			"password_repeat" => '$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta',
		));
		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting...
		$this->assertContains('You have been successfully registered',(string)$controller->flash->success());

		$john = User::FindByLogin("john.doe.joker");
		$this->assertNotEquals('$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta',$john->getPassword());
		$this->assertTrue(MyBlowfish::IsHash($john->getPassword()));

		$this->assertFalse($john->isPasswordCorrect("secret"));
		$this->assertTrue($john->isPasswordCorrect('$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta'));
	}

	function test_edit_password(){
		// no one is logged in
		$this->client->post("users/edit_password",array(
			"current_password" => "secret",
			"password" => "secret_no_more",
			"password_repeat" => "secret_no_more",
		));
		$this->assertEquals(403,$this->client->getStatusCode()); // 403 Forbidden

		// login
		$controller = $this->client->post("logins/create_new",array(
			"login" => "rambo",
			"password" => "secret",
		));
		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting...

		// editing password successfully
		$controller = $this->client->post("users/edit_password",array(
			"current_password" => "secret",
			"password" => "secret_no_more",
			"password_repeat" => "secret_no_more",
		));
		$this->assertEquals(303,$this->client->getStatusCode());
		$this->assertEquals(false,$controller->form->has_errors());

		$rambo = User::FindByLogin("rambo");

		$this->assertFalse($rambo->isPasswordCorrect("secret"));
		$this->assertTrue($rambo->isPasswordCorrect("secret_no_more"));
		$this->assertTrue(MyBlowfish::IsHash($rambo->getPassword()));

		// bad try
		$controller = $this->client->post("users/edit_password",array(
			"current_password" => "BadTry",
			"password" => "Secret2",
			"password_repeat" => "Secret2",
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertEquals(true,$controller->form->has_errors());

		// user gives new password which looks like a proper hash
		$controller = $this->client->post("users/edit_password",array(
			"current_password" => "secret_no_more",
			"password" => '$2a$12$SxmsCtPdm.EP8O6GFpEZ/OcUg5GLTuH6qPEb5T3sdgcJpgBasOzoy', // ramboizer
			"password_repeat" => '$2a$12$SxmsCtPdm.EP8O6GFpEZ/OcUg5GLTuH6qPEb5T3sdgcJpgBasOzoy',
		));
		$this->assertEquals(303,$this->client->getStatusCode());
		$this->assertEquals(false,$controller->form->has_errors());

		$rambo = User::FindByLogin("rambo");

		$this->assertFalse($rambo->isPasswordCorrect("secret_no_more"));
		$this->assertFalse($rambo->isPasswordCorrect("ramboizer"));
		$this->assertTrue($rambo->isPasswordCorrect('$2a$12$SxmsCtPdm.EP8O6GFpEZ/OcUg5GLTuH6qPEb5T3sdgcJpgBasOzoy'));
		$this->assertTrue(MyBlowfish::IsHash($rambo->getPassword()));
		$this->assertNotEquals('$2a$12$SxmsCtPdm.EP8O6GFpEZ/OcUg5GLTuH6qPEb5T3sdgcJpgBasOzoy',$rambo->getPassword());
	}
}
