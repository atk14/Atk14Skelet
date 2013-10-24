<?php
/**
* Here is the list of routes (URIs) to controllers and their actions. Routes are
* considered in order - first matching route will be used.
* 
* Four generic routes at the end of the list are enough for every application.
*
* Search engine friendly URIs can be also defined here. Consider to setup SEF URIs
* at the end of the development or at least not at the begining.
* 
* In your templates build links always this way
*	
*		{a controller=creatures action=detail id=$creature}Details of the creature{/a}
*
* According the matching generic route, link will look like
*
*			/en/creatures/detail/?id=123
*
* When there is a SEF route before the generic form...
*
* 	$this->addRoute("/creature-<id>/","creatures/detail",array("id" => "/[0-9]+/"));
*
* previous link will be changed automatically to the following one
*
*			/creature-123/
*
* And even more when a visitor visits previous link directly (i.e. from a bookmarks),
* he will be transparently redicted to the new one.
*/
class DefaultRouter extends Atk14Router{

	// all the below listed routes are considered to be used in the default namespace ""
	var $namespace = "";

	function setUp(){

		$this->addRoute("/sitemap.xml","sitemaps/index");
		$this->addRoute("/robots.txt","main/robots_txt");

		// shorter password recovery links are nicer in e-mails
		$this->addRoute("/recovery/<token>","en/password_recoveries/recovery");
		$this->addRoute("/obnova/<token>","cs/password_recoveries/recovery");

		// Generic routes follow.
		// Keep them on the end of the list.

		// This is the front page route.
		// The front page will be served in the default language.
		$this->addRoute("/",array(
			"lang" => $this->default_lang,
			"path" => "main/index",
			"title" => ATK14_APPLICATION_NAME,
			"description" => ATK14_APPLICATION_DESCRIPTION,
		));

		$this->addRoute("/<lang>/",array(
			"path" => "main/index"
		));

		$this->addRoute("/<lang>/<controller>/",array(
			"action" => "index"
		));

		$this->addRoute("/<lang>/<controller>/<action>/");
	}
}
