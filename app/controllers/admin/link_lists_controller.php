<?php
class LinkListsController extends AdminController {

	function index(){
		$this->page_title = _("Link lists");

		$this->tpl_data["link_lists"] = LinkList::FindAll(array(
			"order_by" => "system_name",
		));
	}

	function create_new(){
		$this->_create_new([
			"page_title" => _("Creating new link list"),
		]);
	}

	function edit(){
		$this->_edit([
			"page_title" => _("Editing link list"),
		]);
	}

	function set_rank(){
		$this->_set_rank();
	}

	function destroy(){
		$this->_destroy();
	}
}
