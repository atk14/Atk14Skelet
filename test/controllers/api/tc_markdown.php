<?php
/**
 * @fixture users
 */
class TcMarkdown extends TcBase{

	function test_transform(){
		// unauthenticated request must be rejected with 401
		$data = $this->_post("markdown/transform",array(
			"source" => "# Hello",
		),$status_code);
		$this->assertEquals(403,$status_code);
		$this->assertEquals("This service is intended for administrators only",$data[0]);

		// ordinary user login
		$data = $this->_post("logins/create_new",array(
			"login" => "rambo",
			"password" => "secret",
		),$status_code);
		$this->assertEquals(201,$status_code);

		$data = $this->_post("markdown/transform",array(
			"source" => "# Hello",
		),$status_code);
		$this->assertEquals(403,$status_code);
		$this->assertEquals("This service is intended for administrators only",$data[0]);

		// logout
		$this->_post("logins/destroy",[],$status_code);
		$this->assertEquals(200,$status_code);

		// administrator login
		$this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "admin",
		),$status_code);
		$this->assertEquals(201,$status_code);

		// authenticated request works
		$content = $this->client->post("markdown/transform",array(
			"source" => "# Hello",
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertStringContains("<h1>Hello</h1>",$this->client->getContent());

		// raw HTML in source passes through for authenticated users
		$this->client->post("markdown/transform",array(
			"source" => '<div class="custom">text</div>',
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertStringContains('<div class="custom">text</div>',$this->client->getContent());
	}
}
