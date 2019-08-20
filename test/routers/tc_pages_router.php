<?php
/**
 *
 * @fixture pages
 */
class TcPagesRouter extends TcBase {

	function test(){
		global $ATK14_GLOBAL;

		$this->router = new PagesRouter();
		$default_lang = $ATK14_GLOBAL->getDefaultLang();

		// Building

		$uri = $this->assertBuildable(array(
			"lang" => "en",
			"controller" => "pages",
			"action" => "detail",
			"id" => $this->pages["testing_page"]->getId(),
		));
		$this->assertEquals("/testing-page/",$uri);

		$uri = $this->assertBuildable(array(
			"lang" => "cs",
			"controller" => "pages",
			"action" => "detail",
			"id" => $this->pages["testing_page"]->getId(),
		));
		$this->assertEquals("/testovaci-stranka/",$uri);

		$uri = $this->assertBuildable(array(
			"lang" => "en",
			"controller" => "pages",
			"action" => "detail",
			"id" => $this->pages["testing_subpage"]->getId(),
		));
		$this->assertEquals("/testing-page/testing-subpage/",$uri);
	
		// homepage points to main/index action
		$uri = $this->assertBuildable(array(
			"lang" => $default_lang,
			"controller" => "pages",
			"action" => "detail",
			"id" => $this->pages["homepage"]->getId(),
		));
		$this->assertEquals("/",$uri);

		$this->assertNotBuildable(array(
			"lang" => "en",
			"controller" => "pages",
			"action" => "detail",
		));

		// Recognizing

		$ret = $this->assertRecognizable("/testing-page/testing-subpage/",$params);
		$this->assertEquals("pages",$ret["controller"]);
		$this->assertEquals("detail",$ret["action"]);
		$this->assertEquals("en",$ret["lang"]);
		$this->assertEquals($this->pages["testing_subpage"]->getId(),$params["id"]);

		$this->assertNotRecognizable("/bad-slug/");

		$this->assertNotRecognizable("/");
	}
}
