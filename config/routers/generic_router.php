<?php
class GenericRouter extends Atk14Router {

	function setUp(){
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
