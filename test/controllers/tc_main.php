<?php
class TcMain extends TcBase{

	function test_index(){
		$this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
	}

	function test_robots_txt(){
		$this->client->get("main/robots_txt");
		$this->assertEquals("text/plain",$this->client->getContentType());
		$this->assertStringContains("public, max-age=",$this->client->getResponseHeader("Cache-Control"));
	}

	function test_error404(){
		$controller = $this->client->get("main/not_existing_method");
		$this->assertEquals(404,$this->client->getStatusCode());

		$this->client->get("main/error404");
		$this->assertEquals(404,$this->client->getStatusCode());
	}
}
