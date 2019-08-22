<?php
class LinkListItemsController extends AdminController {

	function index() {
		$this->page_title = sprintf(_("Položky v seznamu odkazů '%s'"), $this->link_list->getName());

		$this->tpl_data["finder"] = LinkListItem::Finder(array(
			"conditions" => array("link_list_id=:link_list_id"),
			"bind_ar" => array(
				":link_list_id" => $this->link_list,
			),
			"limit" => null,
		));
	}

	function create_new() {
		$this->_create_new([
			"page_title" => sprintf(_("Nová položka v seznamu odkazů '%s'"),$this->link_list->getName()),
			"create_closure" => function($d){
				$d["link_list_id"] = $this->link_list;
				return LinkListItem::CreateNewRecord($d);
			}

		]);
	}

	function edit(){
		$this->_edit([
			"page_title" => sprintf(_("Editace odkazu '%s'"),$this->link_list_item->getTitle()),
		]);
	}

	function set_rank() {
		$this->_set_rank();
	}

	function destroy(){
		$this->_destroy();
	}

	function _before_filter() {
		$link_list = null;

		if (in_array($this->action, array("index", "create_new"))) {
			$link_list = $this->_find("link_list", "link_list_id");
		}
		if (in_array($this->action, array("edit","set_rank", "destroy"))) {
			$lli = $this->_find("link_list_item");
			$link_list = $lli ? $lli->getLinkList() : null;
		}

		$this->breadcrumbs->add(_("Link Lists"),$this->_link_to("link_lists/index"));
		if($link_list){
			$this->breadcrumbs->add($link_list->getName(),$this->_link_to(array("action" => "link_list_items/index", "link_list_id" => $link_list)));
		}
	}
}
