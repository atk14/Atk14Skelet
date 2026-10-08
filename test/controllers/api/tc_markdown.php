<?php
/**
 * @fixture users
 * @fixture pictures
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

	function test_transform_batch(){
		$data = $this->_post("markdown/transform_batch",array(
			"sources" => json_encode(["# Hello"]),
		),$status_code);
		$this->assertEquals(403,$status_code);

		$this->_login_admin();

		$this->client->post("markdown/transform_batch",array(
			"sources" => json_encode(["# Hello","Some *text*","",'[link](page/)']),
			"base_href" => "/admin/en/pages",
			"format" => "json",
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$contents = json_decode($this->client->getContent(),true);
		$this->assertEquals(4,sizeof($contents));
		$this->assertStringContains("<h1>Hello</h1>",$contents[0]);
		$this->assertStringContains("<em>text</em>",$contents[1]);
		$this->assertEquals("",trim($contents[2]));
		$this->assertStringContains('href="/admin/en/pages/page/"',$contents[3]);

		// empty array
		$this->client->post("markdown/transform_batch",array(
			"sources" => "[]",
			"format" => "json",
		));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertEquals("[]",$this->client->getContent());

		// invalid input
		foreach([
			"" => "sources: This field is required.",
			"nonsense" => "sources: Expecting a JSON encoded array",
			'{"a": "# Hello"}' => "sources: Expecting a JSON encoded array",
			'["# Hello", 123]' => "sources: Every source must be a string",
			json_encode(array_fill(0,501,"x")) => "sources: Too many sources, max. 500 is allowed",
			json_encode([str_repeat("x",50001)]) => "sources: A source is too long, max. 50000 characters is allowed",
		] as $sources => $expected_error){
			$data = $this->_post("markdown/transform_batch",array(
				"sources" => $sources,
			),$status_code);
			$this->assertEquals(400,$status_code,$sources);
			$this->assertEquals([$expected_error],$data);
		}
	}

	function test_editor_preview(){
		$this->_login_admin();
		$source = "[#".$this->pictures["astronaut"]->getId()."]";

		// admin menus are rendered for administrators by default...
		$this->client->post("markdown/transform",array("source" => $source));
		$this->assertEquals(200,$this->client->getStatusCode());
		$this->assertStringContains("iobject--picture",$this->client->getContent());
		$this->assertStringContains("admin-object-menu",$this->client->getContent());

		// ...but not in previews for an editor
		$this->client->post("markdown/transform",array("source" => $source, "editor_preview" => "1"));
		$this->assertStringContains("iobject--picture",$this->client->getContent());
		$this->assertStringNotContains("admin-object-menu",$this->client->getContent());

		$this->client->post("markdown/transform_batch",array("sources" => json_encode([$source,$source]), "editor_preview" => "1", "format" => "json"));
		$contents = json_decode($this->client->getContent(),true);
		$this->assertEquals(2,sizeof($contents));
		foreach($contents as $content){
			$this->assertStringContains("iobject--picture",$content);
			$this->assertStringNotContains("admin-object-menu",$content);
		}
	}

	function _login_admin(){
		$this->_post("logins/create_new",array(
			"login" => "admin",
			"password" => "admin",
		),$status_code);
		$this->assertEquals(201,$status_code);
	}
}
