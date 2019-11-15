<?php
class TcSlugField extends TcBase{

	function test(){
		$this->field = $f = new SlugField(array(
			// "auto_slugify" => true // true is default
		));

		$slug = $this->assertValid("hello-world");
		$this->assertEquals("hello-world",$slug);

		$slug = $this->assertValid("Hi, World!");
		$this->assertEquals("hi-world",$slug);

		$slug = $this->assertValid("300-watts");
		$this->assertEquals("300-watts",$slug);

		$slug = $this->assertValid("300 Watts");
		$this->assertEquals("300-watts",$slug);

		$this->field = $f = new SlugField(array(
			"auto_slugify" => false
		));

		$slug = $this->assertValid("hello-world");
		$this->assertEquals("hello-world",$slug);

		$this->assertInvalid("Hi, World!");

		$this->assertInvalid("300 Watts");
	}
}
