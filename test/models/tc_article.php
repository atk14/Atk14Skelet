<?php
/**
 *
 * @fixture articles
 */
class TcArticle extends TcBase {

	function test_getPageTitle_getPageDescription(){
		$article = $this->articles["interesting_article"];
		$this->assertEquals("Page title",$article->getPageTitle());
		$this->assertEquals("Page title",$article->getPageTitle("en"));
		$this->assertEquals("Název stránky",$article->getPageTitle("cs"));
		$this->assertEquals("Page description",$article->getPageDescription());
		$this->assertEquals("Page description",$article->getPageDescription("en"));
		$this->assertEquals("Popis stránky",$article->getPageDescription("cs"));

		$article = $this->articles["way_too_markdown"];
		$this->assertEquals("Way Too Markdown",$article->getPageTitle());
		$this->assertEquals("Way Too Markdown",$article->getPageTitle("en"));
		$this->assertEquals("Příliš moc Markdownu",$article->getPageTitle("cs"));
		$this->assertEquals("Italic Bold Link www.example.org",$article->getPageDescription());
		$this->assertEquals("Italic Bold Link www.example.org",$article->getPageDescription("en"));
		$this->assertEquals("Italika Tučně Odkaz www.example.org",$article->getPageDescription("cs"));
	}
}
