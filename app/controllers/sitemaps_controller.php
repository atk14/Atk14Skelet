<?php
class SitemapsController extends ApplicationController{
	function index(){
		$available_languages = array_keys($GLOBALS["ATK14_GLOBAL"]->getValue("locale")); // uf... TODO: to be rewritten
		$map_ar = array();
		foreach($available_languages as $lang){
			$map_ar[] = $this->_link_to(array(
				"action" => "detail",
				"lang" => $lang,
				"format" => "xml",
			),array("with_hostname" => true));
		}

		$this->_render_xml($map_ar);
	}

	function detail(){
		$map_ar = array();
		
		$map_ar[] = array(
			"title" => _("Homepage"),
			"action" => "main/index",
			"description" => _("The most important page in the whole universe"),
		);
		$map_ar[] = array(
			"title" => _("Contact"),
			"action" => "main/contact",
			"description" => _("Contact data and quick contact form"),
		);
		$map_ar[] = array(
			"title" => _("About"),
			"action" => "main/about",
			"description" => _("What is this site all about"),
		);
		$map_ar[] = array(
			"title" => _("New user registration"),
			"action" => "users/create_new",
			"description" => _("If you don't have yet an account on this site, this is absolutely must to do procedure"),
		);
		$map_ar[] = array(
			"title" => _("Sign in"),
			"action" => "logins/create_new",
			"description" => _("Sign in to our site"),
		);
		$map_ar[] = array(
			"title" => "API",
			"namespace" => "api",
			"action" => "main/index",
			"description" => _("We offer an awesome restful API"),
		);

		foreach($map_ar as &$item){
			$item += array(
				"namespace" => "",
			);
			if(!isset($item["url"])){
				$item["url"] = $this->_link_to(array("action" => $item["action"], "namespace" => $item["namespace"]),array("with_hostname" => true));
			}
		}

		if($this->params->getString("format")=="xml"){
			$this->_render_xml($map_ar);
			return;
		}

		$this->page_title = _("Sitemap");
		$this->tpl_data["map_ar"] = $map_ar;
	}

	function _render_xml($map_ar){
		$this->render_template = false;
		$this->response->setContentType("text/xml");

		$this->response->writeln('<?xml version="1.0" encoding="UTF-8"?>');
		$this->response->writeln('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		foreach($map_ar as $item){
			$url = is_array($item) ? $item["url"] : $item;
			$this->response->writeln('<url><loc>');
			$this->response->writeln(XMole::toXml($url));
			$this->response->writeln('</loc></url>');
		}
		$this->response->writeln('</urlset>');
	}
}
