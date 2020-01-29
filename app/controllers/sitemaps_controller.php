<?php
class SitemapsController extends ApplicationController{

	function index(){
		$this->render_layout = false;
		$this->response->setContentType("application/xml");
		$this->tpl_data["langs"] = $GLOBALS["ATK14_GLOBAL"]->getSupportedLangs();
	}

	function detail(){
		$this->page_title = $this->breadcrumbs[] = _("Sitemap");

		$this->tpl_data["pages"] = Page::FindAll(array(
			"conditions" => array(
				"parent_page_id IS NULL",
				"visible",
				"indexable"
			),
		));

		$this->tpl_data["articles"] = Article::FindAll(array(
			"condition" => "published_at<:now",
			"bind_ar" => array(
				":now" => now(),
			),
			"order_by" => "published_at DESC",
			"limit" => 20,
		));

		if($this->params->getString("format")=="xml"){
			$this->render_template = false;
			$this->response->setContentType("application/xml");
			$this->response->writeln('<?xml version="1.0" encoding="UTF-8"?>');
			$this->response->writeln('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">');

			// We are gonna extract all links from the rendered HTML snippet
			$content = $this->_render(array("partial" => "detail"));
			preg_match_all('/<a[^>]* href="(.+?)"/',$content,$matches);
			foreach($matches[1] as $url){
				$this->response->writeln(sprintf('<url><loc>%s</loc></url>',h($url)));
			}

			$this->response->write('</urlset>');
		}
	}
}
