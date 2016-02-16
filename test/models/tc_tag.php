<?php
class TcTag extends TcBase{
	function test(){
		$music = Tag::CreateNewRecord(array(
			"tag" => "music"
		));

		// converting to string
		$this->assertEquals("music","$music");

		// is tag deletable?
		$this->assertEquals(true,$music->isDeletable());
		//
		$article = Article::CreateNewRecord(array("author_id" => 1));
		$article->setTags(array($music));
		$this->assertEquals(false,$music->isDeletable());
	}
}
