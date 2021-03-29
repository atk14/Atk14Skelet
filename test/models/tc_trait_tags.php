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
		$spring = $this->tags["spring"];
		$spring_id = $this->tags["spring"]->getId();

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

		// --

		$this->assertEquals(false,$article->containsTag($spring));
		$this->assertEquals(false,$article->containsTag("spring 2000"));
		$this->assertEquals(false,$article->containsTag("spring_2000"));
		$this->assertEquals(false,$article->containsTag($spring->getId()));

		$article->addTag($spring);

		$this->assertEquals(true,$article->containsTag($spring));
		$this->assertEquals(false,$article->containsTag("spring 2000")); // !! false; there is no tag with code == "spring 2000"
		$this->assertEquals(true,$article->containsTag("spring_2000"));
		$this->assertEquals(true,$article->containsTag($spring->getId()));

		// --

		$article->setTags(array());
		$this->assertEquals(array(),$article->getTags());

		$article->setTags(array($fun,$music->getId(),"spring_2000"));
		$tags = $this->assertEquals(array($fun,$music,$spring),$article->getTags());

		// -- hasTag()

		$article->setTags(array());

		$this->assertEquals(false,$article->hasTag($spring));
		$this->assertEquals(false,$article->hasTag("spring_2000"));
		$this->assertEquals(false,$article->hasTag($spring->getId()));

		$article->addTag($spring);

		$this->assertEquals(true,$article->hasTag($spring));
		$this->assertEquals(true,$article->hasTag("spring_2000"));
		$this->assertEquals(true,$article->hasTag($spring->getId()));
	}
}
