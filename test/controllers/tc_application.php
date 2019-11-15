<?php
/**
 *
 */
class TcApplication extends TcBase{

	/**
	 * Usually the meaningful index action is provided by a inheritor.
	 * So good idea should be to service "HTTP 404 Not Found" response on base controller`s index.
	 */
	function test_index(){
		$this->client->get("application/index");
		$this->assertEquals(404,$this->client->getStatusCode());
	}

	function test_error404(){
		$this->client->get("application/non_existing_action");
		$this->assertEquals(404,$this->client->getStatusCode());

		$this->client->get("application/error404");
		$this->assertEquals(404,$this->client->getStatusCode());
	}

	function test_custom_error404_page(){
		$page = Page::GetInstanceByCode("error404");
		$this->assertTrue(!!$page);

		$page->s(array(
			"title_en" => "Well, nothing was found here...",
		));

		$this->client->get("application/non_existing_action");
		$this->assertEquals(404,$this->client->getStatusCode());
		$this->assertContains("Well, nothing was found here...",$this->client->getContent());

		$page->s("code","not_error404");
		Page::GetInstanceByCode("error404",array("refresh_cache" => true));

		$this->client->get("application/non_existing_action");
		$this->assertEquals(404,$this->client->getStatusCode());
		$this->assertNotContains("Well, nothing was found here...",$this->client->getContent());
	}

	function test_error_redirections(){
		ErrorRedirection::CreateNewRecord(array(
			"source_url" => "/docroot/about_us.html",
			"target_url" => "/about-us/"
		));
		ErrorRedirection::RefreshCache();

		$ctrl = $this->client->get("/docroot/about_us.html?page=2");
		$this->assertEquals(301,$this->client->getStatusCode()); // Moved Permanently
		$this->assertEquals("application",$ctrl->controller);
		$this->assertEquals("error404",$ctrl->action);
		$this->assertEquals("/about-us/",$this->client->getLocation());
	}
}
