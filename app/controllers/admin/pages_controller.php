<?php
class PagesController extends AdminController {

	function index() {
		$this->page_title = _("Pages");
		$pages = Page::FindAll(["use_cache" => true]);
		$root_pages = array_filter($pages,function($page){ return is_null($page->getParentPageId()); });
		$root_pages = array_values($root_pages);
		$this->tpl_data["root_pages"] = $root_pages;
	}

	function create_new() {
		$this->_create_new(array(
			"page_title" => _("Create page"),
			"flash_message" => _("The page has been created successfully"),
		));
	}

	function edit() {
		$this->page_title = sprintf(_("Editing page %s"),String4::ToObject($this->page->getTitle())->stripHtml()->toString());

		$this->_add_page_to_breadcrumbs($this->page->getParentPage());

		$this->_save_return_uri();
		$this->form->set_initial($this->page);

		if($this->request->post() && ($d = $this->form->validate($this->params))){

			if($this->form->changed()){
				$this->page->s($d,array("reconstruct_missing_slugs" => true));
				$this->flash->success(_("The page has been updated successfully"));
			}

			if($this->params->defined("save_and_stay")){
				if(!$this->request->xhr()){ $this->_redirect_to($this->request->getRequestUri()); }
				return;
			}

			$this->_redirect_back();
		}

		// kvuli trideni podstranek
		$this->tpl_data["child_pages"] = $this->page->getChildPages();
	}

	function set_rank(){
		$this->_set_rank();
	}

	function destroy(){
		$this->_destroy();
	}

	function _before_filter(){
		if(in_array($this->action,array("edit"))){
			$this->_find("page");
		}
	}
}
