<?php
/**
 *
 * @fixture articles
 */
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
			"title_en" => "Sample Article",
		));
		$this->assertEquals("sample-article",$a1->getSlug("en"));

		$a2 = Article::CreateNewRecord(array(
			"title_en" => "Another Sample Article",
		));
		$this->assertEquals("another-sample-article",$a2->getSlug("en"));

		$a3 = Article::CreateNewRecord(array(
			"title_en" => "Sample Article",
		));
		$this->assertEquals("sample-article-2",$a3->getSlug("en"));
	}

	function test_GetRecordIdBySlug(){
		$testing_article = $this->articles["testing_article"];
		$interesting_article = $this->articles["interesting_article"];

		$id = Slug::GetRecordIdBySlug("articles","testing-article");
		$this->assertEquals($testing_article->getId(),$id);

		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek");
		$this->assertEquals($testing_article->getId(),$id);

		// language detection

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("cs",$lang);

		// correct language passed

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = "cs";
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("cs",$lang);

		// slug & lang mismatch

		$lang = "cs";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang);
		$this->assertNull($id);

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang);
		$this->assertNull($id);

		// correct segment passed

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,"");
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang,"");
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("cs",$lang);

		// invalid segment

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,"xx");
		$this->assertNull($id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang,"xx");
		$this->assertNull($id);

		// searching without segment

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,null);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang,null);
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("cs",$lang);

		// deleting record (cache must be cleared)

		$id = Slug::GetRecordIdBySlug("articles","interesting-article");
		$this->assertEquals($interesting_article->getId(),$id);

		$testing_article->destroy();

		$id = Slug::GetRecordIdBySlug("articles","testing-article");
		$this->assertEquals(null,$id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,null);
		$this->assertEquals(null,$id);

		$id = Slug::GetRecordIdBySlug("articles","interesting-article");
		$this->assertEquals($interesting_article->getId(),$id);
	}
}
