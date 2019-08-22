<?php
class LinkListsController extends AdminController {

	function index(){
		$this->page_title = _("Link lists");
		$this->tpl_data["finder"] = LinkList::Finder(array(
			"limit" => null,
		));
	}

	function create_new(){
		$this->_create_new([
			"page_title" => _("Nový seznam odkazů"),
		]);
	}

	function edit(){
		$this->_edit([
			"page_title" => _("Editing link list"),
		]);
	}

	function destroy(){
		$this->_destroy();
	}
}
