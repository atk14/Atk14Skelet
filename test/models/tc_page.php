<?php
/**
 *
 * @fixture pages
 */
class TcPage extends TcBase {

	function test(){
		$page = $this->pages["testing_page"];

		$this->assertEquals("Testing Page",$page->getTitle());
		$this->assertEquals("Welcome at <em>Testing Page</em>",$page->getTeaser());
		$this->assertEquals("Just Testing Page",$page->getPageTitle());
		$this->assertEquals("This is a testing page",$page->getPageDescription());

		$page->s([
			"page_title_en" => null,
			"page_description_en" => null,
		]);

		$this->assertEquals("Testing Page",$page->getPageTitle());
		$this->assertEquals("Welcome at Testing Page",$page->getPageDescription());
	}
}
