<?php
class ArticlesRouter extends SluggishRouter{

	var $patterns = array(
		"en" => array("index" => "/articles/", "detail" => "/articles/<slug>/"),
		"cs" => array("index" => "/clanky/", "detail" => "/clanky/<slug>/"),
	);
}
