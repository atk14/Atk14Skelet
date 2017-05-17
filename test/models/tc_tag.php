<?php
/**
 *
 * @fixture tags
 * @fixture articles
 */
class TcTag extends TcBase{

	function test(){
		$music = $this->tags["music"];

		// converting to string
		$this->assertEquals("music","$music");

		// is tag deletable?
		$this->assertEquals(true,$music->isDeletable());

		// adding tag to an article
		$this->articles["testing_article"]->setTags(array($music));
		$this->assertEquals(false,$music->isDeletable());
	}
}
