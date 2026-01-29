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

		$articles_uri = Atk14Url::BuildLink("articles/index"); // e.g. "/en/erticles/", "/articles/", "/events/"...

		$this->assertEquals("/",$ctrl->_get_return_uri());
		$this->assertEquals("$articles_uri",$ctrl->_get_return_uri("articles/index"));
		$this->assertEquals("$articles_uri?offset=20",$ctrl->_get_return_uri(["action" => "articles/index", "offset" => 20]));
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
}
