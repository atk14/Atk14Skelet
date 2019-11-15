<?php
/**
 *
 * @fixture tags
 */
class TcTagsField extends TcBase{

	function test(){

		// tag $news comes from migration
		$news = Tag::GetInstanceByCode('news'); // from migration

		// other tags come from fixture
		$music = $this->tags["music"];
		$fun = $this->tags["fun"];
		$wisdom = $this->tags["wisdom"];
		$spring = $this->tags["spring"];

		$this->field = $f = new TagsField(array("unique" => true, "max_tags" => 3, "required" => false));

		$tags = $this->assertValid("");
		$this->assertEquals(array(),$tags);

		$tags = $this->assertValid(", , ,");
		$this->assertEquals(array(),$tags);

		$tags = $this->assertValid("$news");
		$this->assertEquals(array($news),$tags);

		$tags = $this->assertValid("spring 2000");
		$this->assertEquals(array($spring),$tags);

		$tags = $this->assertValid(",$news, spring 2000,,, fun,");
		$this->assertEquals(array($news,$spring,$fun),$tags);

		$err = $this->assertInvalid("$news,music,$news");
		$this->assertEquals(sprintf($f->messages["unique"],$news),$err);

		$err = $this->assertInvalid("$news,music,XXX");
		$this->assertEquals(sprintf($f->messages["no_such_tag"],"XXX"),$err);

		$err = $this->assertInvalid("$news,music,fun,wisdom");
		$this->assertEquals(strtr($f->messages["max_tags"],array("%max%" => 3, "%count%" => 4)),$err);

		// create_tag_if_not_found
		$this->field = $f = new TagsField(array("unique" => true, "create_tag_if_not_found" => true));
		$tags = $this->assertValid("$news,music,XXX");
		$this->assertEquals("XXX",$tags[2]->getTag());
	}
}
