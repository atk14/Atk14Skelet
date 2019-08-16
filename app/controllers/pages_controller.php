<?php
class PagesController extends ApplicationController {

	function detail() {
		$this->page_title = strip_tags($this->page->getTitle());
		$this->page_description = strip_tags($this->page->getTeaser());

		$this->tpl_data["child_pages"] = $this->page->getChildPages();

		$this->_add_page_to_breadcrumbs($this->page);
	}

	function _before_filter(){
		$this->_find("page");
	}
}
