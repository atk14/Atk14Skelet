<?php
class PagesController extends AdminController {

	function index() {
		$this->page_title = _("Pages");
		$this->tpl_data["root_pages"] = Page::FindAll("parent_page_id",null);
	}

	function create_new() {
		$this->_create_new(array(
			"page_title" => _("Create page"),
			"flash_message" => _("The page has been created successfully"),
		));
	}

	function edit() {
		$this->_edit(array(
			"page_title" => _("Edit page"),
			"update_closure" => function($page,$d){
				$page->s($d,array("reconstruct_missing_slugs" => true));
			}
		));

		// kvuli trideni podstranek
		$this->tpl_data["child_pages"] = $this->page->getChildPages();
	}

	function set_rank(){
		$this->_set_rank();
	}

	function destroy(){
		$this->_destroy();
	}
}
