<?php
/**
 *
 * @fixture redirections
 */
class TcRedirection extends TcBase {

	function test(){
		$request = new HttpRequest();

		$request->setUri("/home.php?lang=cs");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/cs/",$r->getDestinationUrl());

		$request->setUri("/home.php?lang=en");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());

		$request->setUri("/home.php?lang=en&utm_source=twiddler");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/",$r->getDestinationUrl());

		$request->setHttpHost("partner.example.com");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/partners/",$r->getDestinationUrl());

		$request->setUri("/home.php?lang=en&utm_source=twiddler");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertEquals("/en/partners/",$r->getDestinationUrl());

		$request->setUri("/home.php?lang=xx");
		$r = Redirection::GetInstanceByHttpRequest($request);
		$this->assertNull($r);
	}

	function test_touch(){
		$redirection = Redirection::CreateNewRecord(array(
			"source_url" => "/attachments/manual.pdf",
			"target_url" => "/public/attachments/manual.pdf", 
		));
		$this->assertNull($redirection->getLastAccessedAt());

		$time = strtotime("2018-07-15 13:50:00");

		$this->assertEquals(true,$redirection->touch($time));
		$this->assertEquals("2018-07-15 13:50:00",$redirection->getLastAccessedAt());

		$this->assertEquals(false,$redirection->touch($time));
		$this->assertEquals(false,$redirection->touch($time-1));

		$this->assertEquals(true,$redirection->touch($time+1));
		$this->assertEquals("2018-07-15 13:50:01",$redirection->getLastAccessedAt());

	}
}
