<?php
/**
 *
 * @fixture pages
 */
class TcPages extends TcBase {

	function test_visible(){
		$page = $this->pages["testing_subpage"];

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("200",$this->client->getStatusCode());

		$page->s("visible",false);

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("404",$this->client->getStatusCode());
	}

	function test_indexable(){
		$page = $this->pages["testing_subpage"];

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("200",$this->client->getStatusCode());
		$this->assertNotContains('<meta name="robots" content="noindex,noarchive">',$this->client->getContent());

		$page->s("indexable",false);

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("200",$this->client->getStatusCode());
		$this->assertContains('<meta name="robots" content="noindex,noarchive">',$this->client->getContent());
	}
}
