<?php
/**
 *
 * @fixture users
 */
class TcUsers extends TcBase{

	// proper parameters for registering a new user
	var $params = array(
		"login" => "john.doe.tester",
		"firstname" => "John",
		"lastname" => "Doe",
		"email" => "john@doe.com",
		"password" => "no_more_fears",
		"password_repeat" => "no_more_fears",

		"return_uri" => "/"
	);

	function test_create_new(){
		// we are visiting the front page
		$controller = $this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertNotContains("john.doe.tester",$this->client->getContent()); // the front page doesn't contain info about user john.doe.tester, actually this user doesn't exist yet
		$this->assertEquals(null,$controller->logged_user);

		// we are visiting the page for user registration
		$controller = $this->client->get("users/create_new");

		if(defined("USER_REGISTRATION_ENABLED") && !constant("USER_REGISTRATION_ENABLED")){
			$this->assertEquals(404,$this->client->getStatusCode());
			return;
		}

		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertEquals(null,$controller->logged_user);

		// passwords don't match to each other
		$params = $this->params;
		$params["password_repeat"] = "no_more_feras";
		$controller = $this->client->post("users/create_new",$params);
		$this->assertEquals(true,$controller->form->has_errors());
		$this->assertEquals(200,$this->client->getStatusCode());

		// we are posting data to the page
		$params = $this->params;
		$controller = $this->client->post("users/create_new",$params);
		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting to $params["return_uri"]...
		$this->assertContains('You have been successfully registered',(string)$controller->flash->success());

		// testing outgoing email
		$mailer = $controller->mailer;
		$this->assertEquals('john@doe.com',$mailer->to);
		$this->assertContains('Thanks for signing up',$mailer->body_html);
		$this->assertContains('login: john.doe.tester',$mailer->body_html);

		// we are visiting the front page again
		$controller = $this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertContains("john.doe.tester",$this->client->getContent()); // now the page contains info about user john.doe.tester
		$this->assertEquals("john.doe.tester",$controller->logged_user->getLogin());

		// the new user has hashed password
		$john = User::FindByLogin("john.doe.tester");
		$this->assertNotEquals("no_more_fears",$john->getPassword());
		$this->assertTrue(MyBlowfish::IsHash($john->getPassword()));

		$this->_logout_user();
	}

	function test_user_gives_a_proper_hash_as_password(){
		// we are posting data to the page
		$params = $this->params;
		$params["password"] = '$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta'; // hash for secret
		$params["password_repeat"] = '$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta';
		$controller = $this->client->post("users/create_new",$params);

		if(defined("USER_REGISTRATION_ENABLED") && !constant("USER_REGISTRATION_ENABLED")){
			$this->assertEquals(404,$this->client->getStatusCode());
			return;
		}

		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting to $params["return_uri"]...
		$this->assertContains('You have been successfully registered',(string)$controller->flash->success());

		$john = User::FindByLogin("john.doe.tester");
		$this->assertNotEquals('$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta',$john->getPassword());
		$this->assertTrue(MyBlowfish::IsHash($john->getPassword()));

		$this->assertFalse($john->isPasswordCorrect("secret"));
		$this->assertTrue($john->isPasswordCorrect('$2a$12$K9oI83nd6DHKaovZleAxcea3YbEuUmKZISehASGthpMzZweUqOhta'));
	}

	function test_edit_email(){
		// no one is logged in
		$this->client->get("users/edit");
		$this->assertEquals(403,$this->client->getStatusCode());

		// user has different login name and email
		$this->_login_user("rambo","secret");

		$data = $this->users["rambo"]->toArray();
		$data["email"] = "info@rambo.com";

		$controller = $this->client->post("users/edit",$data);
		$this->assertTrue($this->client->redirected());
		$this->assertEquals("Your account data has been updated",(string)$controller->flash->success());

		$rambo = User::GetInstanceById($this->users["rambo"]->getId());
		$this->assertEquals("info@rambo.com",$rambo->getEmail());
		$this->assertEquals("rambo",$rambo->getLogin());

		$this->_logout_user();

		// user has the same both login name and email
		$this->_login_user("john@doe.com","Samantha111");

		$data = $this->users["john_doe"]->toArray();
		$data["email"] = "info@doe.com";

		$controller = $this->client->post("users/edit",$data);
		$this->assertTrue($this->client->redirected());
		$this->assertEquals("Your account data has been updated. Next time you should sign-in with login name <em>info@doe.com</em>.",(string)$controller->flash->success());

		$john_doe = User::GetInstanceById($this->users["john_doe"]->getId());
		$this->assertEquals("info@doe.com",$john_doe->getEmail());
		$this->assertEquals("info@doe.com",$john_doe->getLogin());

		$this->_logout_user();

		// another user with the same login name and email
		$this->_login_user("samantha@doe.com","John123");

		$data = $this->users["samantha_doe"]->toArray();
		$data["email"] = "info@doe.com";

		$controller = $this->client->post("users/edit",$data);
		$this->assertFalse($this->client->redirected());

		$this->assertEquals(array("This email address is used by another user."),$controller->form->get_errors("email"));

		$data["email"] = "samantha.doe@gmail.com";

		$controller = $this->client->post("users/edit",$data);
		$this->assertTrue($this->client->redirected());
		$this->assertEquals("Your account data has been updated. Next time you should sign-in with login name <em>samantha.doe@gmail.com</em>.",(string)$controller->flash->success());

		$samantha_doe = User::GetInstanceById($this->users["samantha_doe"]->getId());
		$this->assertEquals("samantha.doe@gmail.com",$samantha_doe->getEmail());
		$this->assertEquals("samantha.doe@gmail.com",$samantha_doe->getLogin());

		$this->_logout_user();
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
		$this->_login_user("rambo","secret");

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

		// password mismatch
		$controller = $this->client->post("users/edit_password",array(
			"current_password" => "secret_no_more",
			"password" => "Secret1",
			"password_repeat" => "Secret2",
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertEquals(true,$controller->form->has_errors());
		$this->assertEquals(["Password doesn't match"],$controller->form->get_errors("password_repeat"));

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

	function _login_user($login,$password){
		$controller = $this->client->post("logins/create_new",array(
			"login" => $login,
			"password" => $password,
		));
		$this->assertEquals(false,$controller->form->has_errors());
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting...
	}

	function _logout_user(){
		$this->client->post("logins/destroy");
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting...
	}
}
