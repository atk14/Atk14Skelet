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

		// There is an internal limit 50, after that uniqid() is added to the slug
		for($i=1;$i<=52;$i++){
			$a = Article::CreateNewRecord(array("title_en" => "Nice Reading"));
			$expected_slug = "nice-reading";
			if($i>=2 && $i<=50){
				$expected_slug .= "-$i";
			}
			if($i>50){
				$this->assertTrue(!!preg_match('/^nice-reading-[a-z0-9]{5,}$/',$a->getSlug("en")));
				return;
			}
			$this->assertEquals($expected_slug,$a->getSlug("en"));
		}
	}

	function test_reconstruct_missing_slugs(){
		// false
		$article = Article::CreateNewRecord(array(
			"title_en" => "Nice Reading",
			"title_cs" => "Krásné čtení",
			"slug_cs" => "krasne-cteni"
		),array(
			"reconstruct_missing_slugs" => false,
		));
		$this->assertEquals("articles-en-".$article->getId(),$article->getSlug("en"));
		$this->assertEquals("krasne-cteni",$article->getSlug("cs"));

		// true
		$article = Article::CreateNewRecord(array(
			"title_en" => "Very Nice Reading",
			"title_cs" => "Moc krásné čtení",
			"slug_cs" => "moc-krasne-cteni"
		),array(
			"reconstruct_missing_slugs" => true,
		));
		$this->assertEquals("very-nice-reading",$article->getSlug("en"));
		$this->assertEquals("moc-krasne-cteni",$article->getSlug("cs"));

		// defaul
		$article = Article::CreateNewRecord(array(
			"title_en" => "Very, Very Nice Reading",
			"title_cs" => "Moc, moc krásné čtení",
			"slug_cs" => "moc-moc-krasne-cteni"
		));
		$this->assertEquals("very-very-nice-reading",$article->getSlug("en"));
		$this->assertEquals("moc-moc-krasne-cteni",$article->getSlug("cs"));
	}

	function test_GetRecordIdBySlug(){
		$testing_article = $this->articles["testing_article"];
		$interesting_article = $this->articles["interesting_article"];

		$testing_article_with_testing_segment = Article::CreateNewRecord(array("slug_en" => null, "slug_cs" => null));

		Slug::CreateNewRecord(array(
			"table_name" => "articles",
			"record_id" => $testing_article_with_testing_segment,
			"lang" => "en",
			"segment" => "testing-segment",
			"slug" => "testing-article-with-testing-segment"
		));

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

		//

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article-with-testing-segment",$lang);
		$this->assertNull($id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article-with-testing-segment",$lang,array("segment" => ""));
		$this->assertNull($id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article-with-testing-segment",$lang,"testing-segment");
		$this->assertEquals($testing_article_with_testing_segment->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article-with-testing-segment",$lang,array("segment" => "testing-segment"));
		$this->assertEquals($testing_article_with_testing_segment->getId(),$id);
		$this->assertEquals("en",$lang);

		// invalid segment

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,"xx");
		$this->assertNull($id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang,"xx");
		$this->assertNull($id);

		// searching without segment

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,array("consider_segment" => false));
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("en",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testovaci-clanek",$lang,array("consider_segment" => false));
		$this->assertEquals($testing_article->getId(),$id);
		$this->assertEquals("cs",$lang);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article-with-testing-segment",$lang,array("consider_segment" => false));
		$this->assertEquals($testing_article_with_testing_segment->getId(),$id);
		$this->assertEquals("en",$lang);

		// deleting record (cache must be cleared)

		$id = Slug::GetRecordIdBySlug("articles","interesting-article");
		$this->assertEquals($interesting_article->getId(),$id);

		$testing_article->destroy();

		$id = Slug::GetRecordIdBySlug("articles","testing-article");
		$this->assertNull($id);

		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","testing-article",$lang,null);
		$this->assertNull($id);

		$id = Slug::GetRecordIdBySlug("articles","interesting-article");
		$this->assertEquals($interesting_article->getId(),$id);

		// usage in a model

		$lang = null;
		$article = Article::GetInstanceBySlug("interesting-article",$lang);
		$this->assertEquals($interesting_article->getId(),$article->getId());
		$this->assertEquals("en",$lang);

		$lang = "cs";
		$article = Article::GetInstanceBySlug("interesting-article",$lang);
		$this->assertNull($article);

		$lang = null;
		$article = Article::GetInstanceBySlug("interesting-article",$lang,"");
		$this->assertEquals($interesting_article->getId(),$article->getId());
		$this->assertEquals("en",$lang);

		$lang = null;
		$article = Article::GetInstanceBySlug("interesting-article",$lang,null);
		$this->assertEquals($interesting_article->getId(),$article->getId());
		$this->assertEquals("en",$lang);

		$lang = null;
		$article = Article::GetInstanceBySlug("interesting-article",$lang,array("segment" => ""));
		$this->assertEquals($interesting_article->getId(),$article->getId());
		$this->assertEquals("en",$lang);

		$lang = null;
		$article = Article::GetInstanceBySlug("interesting-article",$lang,array("segment" => "bad-segment"));
		$this->assertNull($article);

		$lang = null;
		$article = Article::GetInstanceBySlug("testing-article-with-testing-segment",$lang);
		$this->assertNull($article);

		$lang = null;
		$article = Article::GetInstanceBySlug("testing-article-with-testing-segment",$lang,"testing-segment");
		$this->assertEquals($testing_article_with_testing_segment->getId(),$article->getId());

		$lang = null;
		$article = Article::GetInstanceBySlug("testing-article-with-testing-segment",$lang,array("segment" => "testing-segment"));
		$this->assertEquals($testing_article_with_testing_segment->getId(),$article->getId());

		$lang = null;
		$article = Article::GetInstanceBySlug("testing-article-with-testing-segment",$lang,array("consider_segment" => false));
		$this->assertEquals($testing_article_with_testing_segment->getId(),$article->getId());

		// unknown slug

		$cnt1 = $this->dbmole->getQueriesExecuted();
		$this->assertEquals(null,Article::GetInstanceBySlug("unknown-slug"));
		$cnt2 = $this->dbmole->getQueriesExecuted();
		$this->assertEquals(null,Article::GetInstanceBySlug("unknown-slug"));
		$cnt3 = $this->dbmole->getQueriesExecuted();
		$this->assertEquals($cnt1+1,$cnt2);
		$this->assertEquals($cnt2,$cnt3);
	}

	function test_GetRecordIdBySlug_multiple_record_ids_for_one_slug(){
		// the same slug text may be used by two different records, as long as each is in a different language
		// (a custom segment is used here so that creating the articles below - which caches "no slug yet"
		// for the default segment - doesn't shadow the slugs created directly via Slug::CreateNewRecord)
		$segment = "shared-slug-test-segment";

		$article_en = Article::CreateNewRecord(array("slug_en" => null, "slug_cs" => null));
		$article_cs = Article::CreateNewRecord(array("slug_en" => null, "slug_cs" => null));

		Slug::CreateNewRecord(array(
			"table_name" => "articles",
			"record_id" => $article_en,
			"lang" => "en",
			"segment" => $segment,
			"slug" => "shared-slug-text",
		));
		Slug::CreateNewRecord(array(
			"table_name" => "articles",
			"record_id" => $article_cs,
			"lang" => "cs",
			"segment" => $segment,
			"slug" => "shared-slug-text",
		));

		$lang = "en";
		$id = Slug::GetRecordIdBySlug("articles","shared-slug-text",$lang,$segment);
		$this->assertEquals($article_en->getId(),$id);

		$lang = "cs";
		$id = Slug::GetRecordIdBySlug("articles","shared-slug-text",$lang,$segment);
		$this->assertEquals($article_cs->getId(),$id);

		// without an explicit language the default one ("en") wins
		$lang = null;
		$id = Slug::GetRecordIdBySlug("articles","shared-slug-text",$lang,$segment);
		$this->assertEquals($article_en->getId(),$id);
		$this->assertEquals("en",$lang);
	}

	function test_GetRecordIdBySlug_paginates_through_unknown_slugs(){
		$cache_key = "articles,"; // default segment "" with consider_segment=true

		$offsets_property = new ReflectionProperty("Slug","CACHE_OFFSETS");
		$offsets_property->setAccessible(true);

		$cnt1 = $this->dbmole->getQueriesExecuted();
		$this->assertNull(Slug::GetRecordIdBySlug("articles","unknown-slug-page-a"));
		$cnt2 = $this->dbmole->getQueriesExecuted();
		$offset_1 = $offsets_property->getValue()[$cache_key];

		$this->assertNull(Slug::GetRecordIdBySlug("articles","unknown-slug-page-b"));
		$cnt3 = $this->dbmole->getQueriesExecuted();
		$offset_2 = $offsets_property->getValue()[$cache_key];

		$this->assertNull(Slug::GetRecordIdBySlug("articles","unknown-slug-page-c"));
		$cnt4 = $this->dbmole->getQueriesExecuted();
		$offset_3 = $offsets_property->getValue()[$cache_key];

		// each not-yet-cached slug costs exactly one query...
		$this->assertEquals($cnt1+1,$cnt2);
		$this->assertEquals($cnt2+1,$cnt3);
		$this->assertEquals($cnt3+1,$cnt4);

		// ...and advances to the next page of slugs, instead of refetching the same one
		$this->assertEquals(100,$offset_2-$offset_1);
		$this->assertEquals(100,$offset_3-$offset_2);

		// a slug already seen is served from the cache - no query, no further pagination
		$this->assertNull(Slug::GetRecordIdBySlug("articles","unknown-slug-page-a"));
		$cnt5 = $this->dbmole->getQueriesExecuted();
		$offset_4 = $offsets_property->getValue()[$cache_key];
		$this->assertEquals($cnt4,$cnt5);
		$this->assertEquals($offset_3,$offset_4);
	}

	function test_generic_slugs(){
		$article = $this->articles["testing_article"];
		$id = $article->getId();
		$article->s("slug_cs",null);

		$this->assertEquals("testing-article",$article->getSlug());
		$this->assertEquals("articles-cs-$id",$article->getSlug("cs"));

		$lang = null;
		$a = Article::GetInstanceBySlug("testing-article",$lang);
		$this->assertEquals($article->getId(),$a->getId());
		$this->assertEquals("en",$lang);

		$lang = null;
		$a = Article::GetInstanceBySlug("articles-cs-$id",$lang);
		$this->assertEquals($article->getId(),$a->getId());
		$this->assertEquals("cs",$lang);

		$lang = "en";
		$a = Article::GetInstanceBySlug("articles-cs-$id",$lang);
		$this->assertEquals(null,$a);

		$lang = null;
		$a = Article::GetInstanceBySlug("articles-en-$id",$lang);
		$this->assertEquals($article->getId(),$a->getId());
		$this->assertEquals("en",$lang);
	}
}
