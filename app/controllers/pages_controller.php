<?php
class PagesController extends ApplicationController {

	function detail() {
		if(!$this->page->isVisible() && !($this->logged_user && $this->logged_user->isAdmin())){
			return $this->_execute_action("error404");
		}
		$this->page_title = $this->page->getPageTitle();
		$this->page_description = $this->page->getPageDescription();

		$this->tpl_data["child_pages"] = $this->page->getVisibleChildPages();

		$this->_add_page_to_breadcrumbs($this->page);

		if (!$this->page->isIndexable()) {
			$this->head_tags->setMetaTag("robots", "noindex,noarchive");
			$this->head_tags->setMetaTag("googlebot", "noindex");
		}
		$this->head_tags->setCanonical(Atk14Url::BuildLink(["controller" => $this->controller, "action" => $this->action, "id" => $this->page], ["with_hostname" => true]));
	}

	function _before_filter(){
		$this->_find("page");
	}
}
