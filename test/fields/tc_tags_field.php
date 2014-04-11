<?php
class TcTagsField extends TcBase{
	function test(){
		$news = Tag::FindByTag("news");
		$music = Tag::CreateNewRecord(array("tag" => "music"));
		$fun = Tag::CreateNewRecord(array("tag" => "fun"));
		$wisdom = Tag::CreateNewRecord(array("tag" => "wisdom"));
		$spring = Tag::CreateNewRecord(array("tag" => "spring 2000"));

		$this->field = $f = new TagsField(array("unique" => true, "max_tags" => 3, "required" => false));

		$tags = $this->assertValid("");
		$this->assertEquals(array(),$tags);

		$tags = $this->assertValid(", , ,");
		$this->assertEquals(array(),$tags);

		$tags = $this->assertValid("news");
		$this->assertEquals(array($news),$tags);

		$tags = $this->assertValid("spring 2000");
		$this->assertEquals(array($spring),$tags);

		$tags = $this->assertValid(",news, spring 2000,,, fun,");
		$this->assertEquals(array($news,$spring,$fun),$tags);

		$err = $this->assertInvalid("news,music,news");
		$this->assertEquals(sprintf($f->messages["unique"],$news),$err);

		$err = $this->assertInvalid("news,music,XXX");
		$this->assertEquals(sprintf($f->messages["no_such_tag"],"XXX"),$err);

		$err = $this->assertInvalid("news,music,fun,wisdom");
		$this->assertEquals(strtr($f->messages["max_tags"],array("%max%" => 3, "%count%" => 4)),$err);
	}
}
