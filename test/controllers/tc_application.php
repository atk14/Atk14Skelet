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

	function test__get_return_uri(){
		global $HTTP_REQUEST;

		// HTTP referer is not set

		$ctrl = $this->client->get("main/index");

		$this->assertEquals("/",$ctrl->_get_return_uri());
		$this->assertEquals("/articles/",$ctrl->_get_return_uri("articles/index"));
		$this->assertEquals("/articles/?offset=20",$ctrl->_get_return_uri(["action" => "articles/index", "offset" => 20]));
		$this->assertEquals(null,$ctrl->_get_return_uri(null));

		// HTTP referer is set

		$HTTP_REQUEST->setHttpReferer("/admin/en/articles/");

		$ctrl = $this->client->get("main/index");

		$this->assertEquals("/admin/en/articles/",$ctrl->_get_return_uri());

		// With the parameter return_uri

		$ctrl = $this->client->get("main/index",["return_uri" => "/en/users/detail/"]);

		$this->assertEquals("/en/users/detail/",$ctrl->_get_return_uri());

		// With the parameter _return_uri_

		$ctrl = $this->client->get("main/index",["_return_uri_" => "/en/articles/?offset=300"]);

		$this->assertEquals("/en/articles/?offset=300",$ctrl->_get_return_uri());
	}

	function test__save_return_uri(){
		global $HTTP_REQUEST;


		// HTTP referer is not set

		$HTTP_REQUEST->setHttpReferer(null);

		$ctrl = $this->client->get("users/detail");

		$this->assertEquals(null,$ctrl->session->g("return_uris"));

		$ctrl->_save_return_uri();	

		$this->assertEquals([
			md5("/en/users/detail/") => null,
		],$ctrl->session->g("return_uris"));

		$this->assertEquals([
			"_return_uri_" => null,
		],$ctrl->form->atk14_hidden_fields);

		$HTTP_REQUEST->setHttpReferer(null);

		$ctrl = $this->client->post("users/detail",["_return_uri_" => ""]);

		$this->assertEquals("/en/users/",$ctrl->_get_return_uri());

		// HTTP referer is set

		$HTTP_REQUEST->setHttpReferer("/admin/en/");

		$ctrl = $this->client->get(Atk14Url::BuildLink(["namespace" => "admin", "action" => "articles/edit", "id" => 1]));

		// no change made in the session return_uris
		$this->assertEquals([
			md5("/en/users/detail/") => null,
		],$ctrl->session->g("return_uris"));

		$ctrl->_save_return_uri();	

		$this->assertEquals([
			md5("/en/users/detail/") => null,
			md5("/admin/en/articles/edit/?id=1") => "/admin/en/",
		],$ctrl->session->g("return_uris"));

		$this->assertEquals([
			"_return_uri_" => "/admin/en/",
		],$ctrl->form->atk14_hidden_fields);

		$HTTP_REQUEST->setHttpReferer(null);

		$ctrl = $this->client->post(Atk14Url::BuildLink(["namespace" => "admin", "action" => "articles/edit", "id" => 1]),["_return_uri_" => ""]);

		$this->assertEquals("/admin/en/",$ctrl->_get_return_uri());
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

		$ctrl = $this->client->get("https://www.example.com/docroot/about_us.html?page=2");
		$this->assertEquals(301,$this->client->getStatusCode()); // Moved Permanently
		$this->assertEquals("application",$ctrl->controller);
		$this->assertEquals("error404",$ctrl->action);
		$this->assertEquals("/about-us/",$this->client->getLocation());

		$ctrl = $this->client->get("https://www.example.com/docroot/about_them.html");
		$this->assertEquals(404,$this->client->getStatusCode()); // Moved Permanently
		$this->assertEquals("application",$ctrl->controller);
		$this->assertEquals("error404",$ctrl->action);
	}

	function test__create_newsletter_subscription_request(){
		$ctrl = $this->client->get("main/index");

		$nsr = $ctrl->_create_newsletter_subscription_request("john.doe@example.com");
		$this->assertTrue(is_object($nsr));

		NewsletterSubscriber::SignUp("John.Doe@EXAMPLE.COM");

		$nsr2 = $ctrl->_create_newsletter_subscription_request("john.doe@example.com");
		$this->assertNull($nsr2);

		$nsr3 = $ctrl->_create_newsletter_subscription_request("john.doe@example.com",[],["create_request_if_subscription_exists" => true]);
		$this->assertTrue(is_object($nsr3));

		$nsr4 = $ctrl->_create_newsletter_subscription_request("john.doe@example.com",[],["create_request_if_subscription_exists" => false]);
		$this->assertNull($nsr4);

		$nsr5  = $ctrl->_create_newsletter_subscription_request("Samantha.doe@example.com",[],["create_request_if_subscription_exists" => false]);
		$this->assertTrue(is_object($nsr5));
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
