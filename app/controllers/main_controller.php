<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/default.tpl
	 */
	function index(){
		$this->page_title = _("Welcome at ATK14 Mini Skelet!");
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");
	}
}
