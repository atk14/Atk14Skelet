<?php
/**
 *
 * @fixture error_redirections
 */
class TcErrorRedirection extends TcBase {

	function test(){
		ErrorRedirection::RefreshCache();

		$request = new HttpRequest();

		$request->setUri("/scripts/home.php?lang=cs");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/cs/",$r->getDestinationUrl());

		$request->setUri("/scripts/home.php?lang=en");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());

		$request->setUri("/scripts/home.php?lang=en&utm_source=twiddler");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());

		$request->setHttpHost("partner.example.com");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/partners/",$r->getDestinationUrl());

		$request->setUri("/scripts/home.php?lang=en&utm_source=twiddler");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/partners/",$r->getDestinationUrl());

		$request->setUri("/scripts/home.php?lang=xx");
		$r = ErrorRedirection::GetInstanceByHttpRequest($request);
		$this->assertNull($r);
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
}
