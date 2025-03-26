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
		$this->assertStringContains("Well, nothing was found here...",$this->client->getContent());

		$page->s("code","not_error404");
		Page::GetInstanceByCode("error404",array("refresh_cache" => true));

		$this->client->get("application/non_existing_action");
		$this->assertEquals(404,$this->client->getStatusCode());
		$this->assertStringNotContains("Well, nothing was found here...",$this->client->getContent());
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

	function test__create_newsletter_subscription_request(){
		$ctrl = $this->client->get("main/index");

		$nsr = $ctrl->_create_newsletter_subscription_request("john.doe@example.com");
		$this->assertTrue(is_object($nsr));

		NewsletterSubscriber::SignUp("John.Doe@EXAMPLE.COM");

		$nsr2 = $ctrl->_create_newsletter_subscription_request("john.doe@example.com");
		$this->assertTrue(is_object($nsr2));

		$nsr3 = $ctrl->_create_newsletter_subscription_request("john.doe@example.com",["create_request_if_subscription_exists" => false]);
		$this->assertNull($nsr3);

		$nsr4  = $ctrl->_create_newsletter_subscription_request("Samantha.doe@example.com",["create_request_if_subscription_exists" => false]);
		$this->assertTrue(is_object($nsr4));
	}

	function test__sign_up_for_newsletter(){
		$ctrl = $this->client->get("main/index");

		$ns2 = $ctrl->_sign_up_for_newsletter("john.doe@example.com");
		$this->assertTrue(is_object($ns2));
		$this->assertTrue(strlen($ctrl->mailer->subject)>0);

		$ctrl2 = $this->client->get("main/index");

		$ns = $ctrl2->_sign_up_for_newsletter("samantha.doe@example.com",[],["send_notification" => false]);
		$this->assertTrue(is_object($ns));
		$this->assertEquals("",$ctrl2->mailer->subject);

		$ctrl3 = $this->client->get("main/index");

		$ns3 = $ctrl3->_sign_up_for_newsletter("John.Doe@EXAMPLE.COM");
		$this->assertTrue(is_object($ns3));
		$this->assertEquals("",$ctrl3->mailer->subject); // nothing is notified, the subscription already exists
	}
}
