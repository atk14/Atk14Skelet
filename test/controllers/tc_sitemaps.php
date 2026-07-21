<?php
/**
 *
 * @fixture pages
 */
class TcSitemaps extends TcBase {

	function test(){
		$client = $this->client;
		
		$client->get("sitemaps/index");
		$this->assertEquals("application/xml",$client->getContentType());
		$this->assertStringContains("public, max-age=",$client->getResponseHeader("Cache-Control"));

		$client->get("sitemaps/detail");
		$this->assertEquals("text/html",$client->getContentType());
		$this->assertStringContains("private",$client->getResponseHeader("Cache-Control"));

		$client->get("sitemaps/detail",["format" => "xml"]);
		$this->assertEquals("application/xml",$client->getContentType());
		$this->assertStringContains("public, max-age=",$client->getResponseHeader("Cache-Control"));
	}

	function test_content(){
		$page = $this->pages["testing_page"];
		$page2 = $this->pages["another_testing_page"];

		$this->client->get("sitemaps/detail");
		$this->assertStringContains($page->getTitle(),$this->client->getContent());
		$this->assertStringContains($page2->getTitle(),$this->client->getContent());

		$page->s("indexable",false);
		$page2->s("visible",false);

		$this->client->get("sitemaps/detail");
		$this->assertStringNotContains($page->getTitle(),$this->client->getContent());
		$this->assertStringNotContains($page2->getTitle(),$this->client->getContent());
	}
}
