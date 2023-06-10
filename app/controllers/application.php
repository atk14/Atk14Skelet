<?php
require_once(__DIR__."/application_base.php");

class ApplicationController extends ApplicationBaseController{

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

	function _add_user_detail_breadcrumb(){
		if(!$this->logged_user){ return; }

		$title = _("User profile");
		
		if("$this->controller/$this->action"=="users/detail"){
			$this->breadcrumbs[] = $title;
			return;
		}

		$this->breadcrumbs[] = [$title,"users/detail"];
	}
}
