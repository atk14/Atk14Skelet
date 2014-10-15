<?php
class SitemapsController extends ApplicationController{
	function index(){
		$this->render_layout = false;
		$this->response->setContentType("text/xml");
		$this->tpl_data["langs"] = $GLOBALS["ATK14_GLOBAL"]->getSupportedLangs();
	}

	function detail(){
		$this->page_title = _("Sitemap");

		if($this->params->getString("format")=="xml"){
			$this->render_template = false;
			$this->response->setContentType("text/xml");
			$this->response->writeln('<?xml version="1.0" encoding="UTF-8"?>');
			$this->response->writeln('<urlset>');

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
