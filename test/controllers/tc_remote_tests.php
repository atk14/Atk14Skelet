<?php
class TcRemoteTests extends TcBase {

	function test(){
		$client = $this->client;

		$client->get("remote_tests/success");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue(!!preg_match('/^ok/',$client->getContent()));

		$client->get("remote_tests/fail");
		$this->assertEquals(500,$client->getStatusCode());
		$this->assertTrue(!!preg_match('/^fail/',$client->getContent()));

	}
}
