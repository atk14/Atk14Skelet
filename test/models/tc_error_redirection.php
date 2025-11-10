<?php
/**
 *
 * @fixture error_redirections
 */
class TcErrorRedirection extends TcBase {

	function test(){
		ErrorRedirection::RefreshCache();

		$request = new HttpRequest();

		$request->setUrl("https://www.example.com/scripts/home.php?lang=cs");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/cs/",$r->getDestinationUrl());
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertEquals("/cs/",$r->getDestinationUrl());

		$request->setUrl("https://www.example.com/scripts/home.php?lang=en");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertEquals("/en/",$r->getDestinationUrl());

		$request->setUrl("https://www.example.com/scripts/home.php?lang=en&utm_source=twiddler");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertNull($r);

		$request->setUrl("https://partner.example.com/scripts/home.php?lang=en&utm_source=twiddler");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/partners/",$r->getDestinationUrl());
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertNull($r);

		$request->setUrl("https://www.example.com/scripts/home.php?lang=en&utm_source=twiddler");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertNull($r);

		$request->setUrl("https://www.example.com/scripts/home.php?lang=xx");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertNull($r);
		$r = ErrorRedirection::GetInstanceByHttpRequest($request,["strict_match" => true]);
		$this->assertNull($r);

		// URI with parameters
		$request->setUrl("https://www.example.com/old-site/about-us/");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/about-us/",$r->getDestinationUrl());
		$request->setUrl("https://www.example.com/old-site/about-us/?param1=val1&param2=val2");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/about-us/",$r->getDestinationUrl());
	}

	function test_touch(){
		$redirection = ErrorRedirection::CreateNewRecord(array(
			"source_url" => "/attachments/manual.pdf",
			"target_url" => "/public/attachments/manual.pdf", 
		));
		$redirection2 = ErrorRedirection::CreateNewRecord(array(
			"source_url" => "/site/about-us/",
			"target_url" => "/about-us/", 
		));
		ErrorRedirection::RefreshCache();

		$this->assertNull($redirection->getLastAccessedAt());

		$time = strtotime("2018-07-15 13:50:00");

		$this->assertEquals(true,$redirection->touch($time));
		$this->assertEquals("2018-07-15 13:50:00",$redirection->getLastAccessedAt());

		$this->assertEquals(false,$redirection->touch($time));
		$this->assertEquals(false,$redirection->touch($time-1));

		$this->assertEquals(true,$redirection->touch($time+1));
		$this->assertEquals("2018-07-15 13:50:01",$redirection->getLastAccessedAt());

		$redirection2 = ErrorRedirection::GetInstanceById($redirection2);
		$this->assertEquals(null,$redirection2->getLastAccessedAt());
	}

	function test__NormalizeUrl(){
		$this->assertEquals("https://www.example.com/document.pdf",ErrorRedirection::_NormalizeUrl("https://www.example.com/document.pdf"));
		$this->assertEquals("https://www.example.com/search/?q=test",ErrorRedirection::_NormalizeUrl("https://www.example.com/search/?q=test"));
		$this->assertEquals("https://www.example.com/styles.css?v1234",ErrorRedirection::_NormalizeUrl("https://www.example.com/styles.css?v1234"));
		$this->assertEquals("/search/?q=test",ErrorRedirection::_NormalizeUrl("/search/?q=test"));
		$this->assertEquals("/search/?q=lampi%C4%8Dka+Moleskine",ErrorRedirection::_NormalizeUrl("/search/?q=lampička+Moleskine"));
		$this->assertEquals("/search/?q=lampi%C4%8Dka+Moleskine&offset=100",ErrorRedirection::_NormalizeUrl("/search/?q=lampička+Moleskine&offset=100"));

		$this->assertEquals("/search/?q=lampi%C4%8Dka+Moleskine&offset=100",ErrorRedirection::_NormalizeUrl("/search/?&q=lampička+Moleskine&offset=100"));
		$this->assertEquals("/search/?lampi%C4%8Dka+Moleskine&offset=100",ErrorRedirection::_NormalizeUrl("/search/?lampička+Moleskine&offset=100"));
	}

	function test__GetPossibleSourceUrls(){
		$this->assertEquals([
			"https://www.example.com/document.pdf",
			"//www.example.com/document.pdf",
			"/document.pdf"
		],ErrorRedirection::_GetPossibleSourceUrls("https://www.example.com/document.pdf"));

		$this->assertEquals([
			"https://www.example.com/search/?q=test",
			"//www.example.com/search/?q=test",
			"/search/?q=test",
			"https://www.example.com/search/",
			"//www.example.com/search/",
			"/search/",
		],ErrorRedirection::_GetPossibleSourceUrls("https://www.example.com/search/?q=test"));

		$this->assertEquals([
			"https://www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
			"//www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
			"/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
			"https://www.example.com/search/?q=lampi%C4%8Dka+Moleskine",
			"//www.example.com/search/?q=lampi%C4%8Dka+Moleskine",
			"/search/?q=lampi%C4%8Dka+Moleskine",
			"https://www.example.com/search/",
			"//www.example.com/search/",
			"/search/",
		],ErrorRedirection::_GetPossibleSourceUrls("https://www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100"));

		$this->assertEquals([
			"https://www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
			"//www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
			"/search/?q=lampi%C4%8Dka+Moleskine&offset=100",
		],ErrorRedirection::_GetPossibleSourceUrls("https://www.example.com/search/?q=lampi%C4%8Dka+Moleskine&offset=100",["strict_match" => true]));
	}
}
