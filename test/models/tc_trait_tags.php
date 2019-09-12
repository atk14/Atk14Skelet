<?php
/**
 *
 * @fixture articles
 * @fixture tags
 */
class TcTraitTags extends TcBase {

	function test(){
		$article = $this->articles["testing_article"];
		$fun = $this->tags["fun"];
		$music = $this->tags["music"];

		$this->assertEquals(array(),$article->getTags());
		$this->assertEquals(false,$article->containsTag($fun));
		$this->assertEquals(false,$article->containsTag($music));

		$article->setTags(array($fun));
		$tags = $article->getTags();
		$this->assertEquals(array($fun),$tags);
		$this->assertEquals(true,$article->containsTag($fun));
		$this->assertEquals(false,$article->containsTag($music));

		$article->addTag($music);
		$tags = $article->getTags();
		$this->assertEquals(array($fun,$music),$tags);
		$this->assertEquals(true,$article->containsTag($fun));
		$this->assertEquals(true,$article->containsTag($music));
	}
}
