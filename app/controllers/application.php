<?php
require_once(__DIR__."/application_base.php");

class ApplicationController extends ApplicationBaseController{
	
	function _add_page_to_breadcrumbs($page){
		$pages = array($page);
		$p = $page;
		while($parent = $p->getParentPage()){
			$pages[] = $parent;
			$p = $parent;
		}
		$pages = array_reverse($pages);
		foreach($pages as $p){
			$this->breadcrumbs[] = array($p->getTitle(),$this->_link_to(array("action" => "pages/detail", "id" => $p)));
		}
	}
}
