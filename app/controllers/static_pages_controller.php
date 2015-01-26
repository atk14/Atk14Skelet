<?php
class StaticPagesController extends ApplicationController {
	function detail() {
		$this->page_title = strip_tags($this->static_page->getTitle());
		$this->page_description = strip_tags($this->static_page->getTeaser());

		$this->tpl_data["child_pages"] = $this->static_page->getChildStaticPages();
	}

	function _before_filter(){
		$this->_find("static_page");
	}
}
