<?php
/**
 * Here is the list of routes (URIs) to controllers and their actions. Routes are
 * considered in order - the first matching route will be used.
 * 
 * There are four generic routes at the end of the list. If you don't have any extra
 * needs, these four routes are enough for you.
 *
 * Search engine friendly URIs can be also defined here. Consider to setup SEF URIs
 * at the end of the development or at least not at the beginning.
 * 
 * In your templates build links always this way
 *
 *      {a controller=creatures action=detail id=$creature}Details of the creature{/a}
 *
 * According to the matching generic route, the following URI will be rendered
 *
 *      /en/creatures/detail/?id=123
 *
 * Now you can prepend a SEF route like this
 *
 *      $this->addRoute("/creature-<id>/","creatures/detail",array("id" => "/[0-9]+/"));
 *
 * the previous link will be changed automatically to the following one
 *
 *      /creature-123/
 *
 * Keep in mind that there is a useful script for URI recognition
 *
 *      $ ./scripts/recognize_route http://myapp.localhost/creature-123/
 *
 * For more information about routing see http://book.atk14.net/czech/routing/
 */
class DefaultRouter extends Atk14Router{

	// all the routes in this file are applicable only in the default (empty) namespace
	var $namespace = "";

	function setUp(){

		$this->addRoute("/sitemap.xml","sitemaps/index");
		$this->addRoute("/sitemap/","en/sitemaps/detail");
		$this->addRoute("/mapa-stranek/","cs/sitemaps/detail");

		$this->addRoute("/robots.txt","main/robots_txt");

		// Shorter password recovery links are nicer in emails:
		//   generic route: http://example.com/en/password_recoveries/recovery/?token=123.xyz
		//      nice route: http://example.cz/recovery/123.xyz
		$this->addRoute("/recovery/<token>","en/password_recoveries/recovery");
		$this->addRoute("/obnova/<token>","cs/password_recoveries/recovery");

		// Nice URIs for attachments
		$this->addRoute("/attachment/<token>/<filename>","$this->default_lang/attachments/detail");
		// .. and files
		$this->addRoute("/file/<token>/<filename>","$this->default_lang/files/detail");

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
