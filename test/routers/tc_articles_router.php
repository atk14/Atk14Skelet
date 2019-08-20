<?php
/**
 *
 * @fixture articles
 */
class TcArticlesRouter extends TcBase {

	function test(){
		$this->router = new ArticlesRouter();

		// Building

		$uri = $this->assertBuildable(array(
			"lang" => "en",
			"controller" => "articles",
			"action" => "detail",
			"id" => $this->articles["testing_article"]->getId(),
		));
		$this->assertEquals("/articles/testing-article/",$uri);

		$uri = $this->assertBuildable(array(
			"lang" => "cs",
			"controller" => "articles",
			"action" => "detail",
			"id" => $this->articles["testing_article"]->getId(),
		));
		$this->assertEquals("/clanky/testovaci-clanek/",$uri);

		$this->assertNotBuildable(array(
			"lang" => "cs",
			"controller" => "articles",
			"action" => "detail",
		));

		$uri = $this->assertBuildable(array(
			"lang" => "en",
			"controller" => "articles",
			"action" => "index",
		));
		$this->assertEquals("/articles/",$uri);

		$uri = $this->assertBuildable(array(
			"lang" => "cs",
			"controller" => "articles",
			"action" => "index",
		));
		$this->assertEquals("/clanky/",$uri);

		// Recognizing

		$params = array();
		$ret = $this->assertRecognizable("/articles/testing-article/",$params);
		$this->assertEquals("articles",$ret["controller"]);
		$this->assertEquals("detail",$ret["action"]);
		$this->assertEquals("en",$ret["lang"]);
		$this->assertEquals($this->articles["testing_article"]->getId(),$params["id"]);

		$ret = $this->assertNotRecognizable("/bad-prefix/testing-article/");

		$ret = $this->assertNotRecognizable("/articles/non-existing-article/");

		$params = array();
		$ret = $this->assertRecognizable("/articles/",$params);
		$this->assertEquals("articles",$ret["controller"]);
		$this->assertEquals("index",$ret["action"]);
		$this->assertEquals("en",$ret["lang"]);
		$this->assertEquals(array(),$params);

		$params = array();
		$ret = $this->assertRecognizable("/clanky/",$params);
		$this->assertEquals("articles",$ret["controller"]);
		$this->assertEquals("index",$ret["action"]);
		$this->assertEquals("cs",$ret["lang"]);
		$this->assertEquals(array(),$params);
	}
}
