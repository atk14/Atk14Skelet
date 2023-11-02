<?php
/**
 *
 * @fixture pages
 */
class TcPage extends TcBase {

	function test(){
		$page = $this->pages["testing_page"];

		$this->assertEquals("Testing Page",$page->getTitle());
		$this->assertEquals("Welcome to <em>Testing Page</em>",$page->getTeaser());
		$this->assertEquals("Just Testing Page",$page->getPageTitle());
		$this->assertEquals("This is a testing page",$page->getPageDescription());

		$page->s([
			"page_title_en" => null,
			"page_description_en" => null,
		]);

		$this->assertEquals("Testing Page",$page->getPageTitle());
		$this->assertEquals("Welcome to Testing Page",$page->getPageDescription());
	}

	function test_isIndexable(){
		$page = $this->pages["testing_page"];
		$subpage = $this->pages["testing_subpage"];


		$this->assertTrue($page->isIndexable());
		$this->assertTrue($subpage->isIndexable());

		$page->s("indexable",false);
		Cache::Clear();

		$this->assertFalse($page->isIndexable());
		$this->assertFalse($subpage->isIndexable());

		$this->assertFalse($page->isIndexable(false));
		$this->assertTrue($subpage->isIndexable(false));

		$page->s("indexable",true);
		$subpage->s("indexable",false);
		Cache::Clear();

		$this->assertTrue($page->isIndexable());
		$this->assertFalse($subpage->isIndexable());

		$this->assertTrue($page->isIndexable(false));
		$this->assertFalse($subpage->isIndexable(false));
	}
}
