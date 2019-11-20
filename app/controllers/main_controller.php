<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/default.tpl
	 */
	function index(){
		$this->page_title = _("Welcome!");

		$page = $this->tpl_data["page"] = Page::GetInstanceByCode("homepage");
		if($page){
			$this->page_title = $page->getPageTitle();
			$this->page_description = $page->getPageDescription();
		}
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");
	}
}
