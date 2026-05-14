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
		Cache::Clear();

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("404",$this->client->getStatusCode());
	}

	function test_indexable(){
		$page = $this->pages["testing_subpage"];

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("200",$this->client->getStatusCode());
		$this->assertStringNotContains('<meta name="robots" content="noindex,nofollow,noarchive">',$this->client->getContent());

		$page->s("indexable",false);
		Cache::Clear();

		$this->client->get("pages/detail", array("id" => $page));
		$this->assertEquals("200",$this->client->getStatusCode());
		$this->assertStringContains('<meta name="robots" content="noindex,noarchive">',$this->client->getContent());
	}
}
