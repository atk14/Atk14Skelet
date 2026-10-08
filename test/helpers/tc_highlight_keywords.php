<?php
class TcHighlightKeywords extends TcBase {

	function test(){
		Atk14Require::Helper("block.highlight_keywords");

		$repeat = false;
		$template = null;

		$this->assertEquals('<mark>Hello</mark> World!',smarty_block_highlight_keywords(["keywords" => "hello"],"Hello World!",$template,$repeat));
		$this->assertEquals('<i class="highlight">Hello</i> World!',smarty_block_highlight_keywords(["keywords" => "hello", "tag" => '<i class="highlight">'],"Hello World!",$template,$repeat));

		foreach([
			"",
			" ",
			"NOT MATCH"
		] as $keywords){
			$this->assertEquals('Hello World!',smarty_block_highlight_keywords(["keywords" => $keywords],"Hello World!",$template,$repeat));
		}
	}
}
