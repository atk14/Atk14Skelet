<?php
class TcSlug extends TcBase {

	function test_StringToSluggish(){
		$this->assertEquals("hell-yeah",Slug::StringToSluggish("Hell Yeah! :)"));
		$this->assertEquals("hell-yeah-123",Slug::StringToSluggish("Hell Yeah! :)","123"));

		$very_long_name = str_repeat("a",SLUG_MAX_LENGTH * 2); // 400 x a
		$slug = Slug::StringToSluggish($very_long_name,"456");
		$this->assertEquals(SLUG_MAX_LENGTH,strlen($slug));
		$this->assertTrue(!!preg_match('/aaa-456$/',$slug));
	}

	function test_handle_slug_collision(){
		$a1 = Article::CreateNewRecord(array(
			"title_en" => "Testing Article",
		));
		$this->assertEquals("testing-article",$a1->getSlug("en"));

		$a2 = Article::CreateNewRecord(array(
			"title_en" => "Another Testing Article",
		));
		$this->assertEquals("another-testing-article",$a2->getSlug("en"));

		$a3 = Article::CreateNewRecord(array(
			"title_en" => "Testing Article",
		));
		$this->assertEquals("testing-article-2",$a3->getSlug("en"));
	}
}
