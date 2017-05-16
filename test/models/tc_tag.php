<?php
/**
 *
 * @fixture tags
 */
class TcTag extends TcBase{

	function test(){
		$music = $this->tags["music"];

		// converting to string
		$this->assertEquals("Music","$music");

		// is tag deletable?
		$this->assertEquals(true,$music->isDeletable());
		//
		$article = Article::CreateNewRecord(array("author_id" => 1));
		$article->setTags(array($music));
		$this->assertEquals(false,$music->isDeletable());
	}
}
