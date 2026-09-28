<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/default.tpl
	 */
	function index(){
		$this->page_title = ATK14_APPLICATION_NAME;
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");

		$this->response->clearHeader("Pragma");
		$this->response->setHeader("Cache-Control","public, max-age=86400");
		$this->response->setHeader("Last-Modified",gmdate("D, d M Y H:i:s", filemtime(ATK14_DOCUMENT_ROOT."/app/views/main/robots_txt.tpl"))." GMT");
	}
}
