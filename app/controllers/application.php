<?php
require_once(__DIR__."/application_base.php");

class ApplicationController extends ApplicationBaseController{

	function error404(){
		if($this->request->xhr()){
			return parent::error404();
		}

		if($this->_redirected_on_error404()){
			return;
		}

		$page = Page::GetInstanceByCode("error404");
		if(!$page){
			return parent::error404();
		}

		$this->page_title = $page->getTitle();
		$this->page_description = $page->getPageDescription();
		$this->_add_page_to_breadcrumbs($page);
		$this->response->setStatusCode(404);
		$this->tpl_data["page"] = $page;
		$this->template_name = "pages/detail";
	}
	
	function _add_page_to_breadcrumbs($page){
		$pages = array($page);
		$p = $page;
		while($parent = $p->getParentPage()){
			$p = $parent;
			if($p->getCode()=="homepage"){ continue; }
			$pages[] = $p;
		}
		$pages = array_reverse($pages);
		foreach($pages as $p){
			$this->breadcrumbs[] = array($p->getTitle(),$this->_link_to(array("action" => "pages/detail", "id" => $p)));
		}
	}
}
