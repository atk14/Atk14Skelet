<?php
class PagesController extends ApplicationController {

	function detail() {
		if(!$this->page->isVisible()){
			return $this->_execute_action("error404");
		}

		$this->page_title = strip_tags($this->page->getTitle());
		$this->page_description = strip_tags($this->page->getTeaser());

		$this->tpl_data["child_pages"] = $this->page->getVisibleChildPages();

		$this->_add_page_to_breadcrumbs($this->page);
	}

	function _before_filter(){
		$this->_find("page");
	}
}
