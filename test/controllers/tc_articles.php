<?php
/**
 *
 * @fixture articles
 */
class TcArticles extends TcBase {

	function test(){
		$this->client->get("articles/detail",array("id" => $this->articles["testing_article"]->getId()));
		$this->assertContains(">Testing Article</h1>",$this->client->getContent());
		$this->assertContains("<title>Testing Article",$this->client->getContent());
		$this->assertContains('<meta name="description" content="Testing teaser">',$this->client->getContent());

		$this->client->get("articles/detail",array("id" => $this->articles["interesting_article"]->getId()));
		$this->assertContains(">Interesting Article</h1>",$this->client->getContent());
		$this->assertContains("<title>Page title",$this->client->getContent());
		$this->assertContains('<meta name="description" content="Page description">',$this->client->getContent());
	}
}
