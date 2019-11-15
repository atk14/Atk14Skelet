<?php
require(__DIR__ . "/iobjects_base.php");
class GalleriesController extends IobjectsBaseController {

	function create_new(){
		$this->form->set_hidden_field("return_uri",$this->_get_return_uri());
		$return_uri = $this->params->getString("return_uri");

		$this->_create_new(array(
			"page_title" => _("Nová fotogalerie"),

			"create_closure" => function($d){
				$gallery = Gallery::CreateNewRecord($d);
				IobjectLink::CreateNewRecord(array(
					"iobject_id" => $gallery,
					"linked_table" => $this->table_name,
					"linked_record_id" => $this->record_id,
				));
				return $gallery;
			},

			"redirect_to" => function($gallery) use ($return_uri){
				return Atk14Url::BuildLink(array(
					"action" => "galleries/detail",
					"id" => $gallery,
					"return_uri" => $return_uri,
				),array("connector" => "&"));
			}
		));
	}

	function detail(){
		$title = _("Fotogalerie");
		if($this->gallery->getTitle()){ $title .= ": ".strip_tags($this->gallery->getTitle()); }
		$this->page_title = $title;

		$this->_set_up_breadcrumbs();

		$return_uri = $this->_get_return_uri();
		$form = $this->_get_form("gallery_items/create_new_form");
		$form->set_hidden_field("return_uri",$return_uri);
		$form->set_action($this->_link_to(array("action" => "gallery_items/create_new", "gallery_id" => $this->gallery)));
		$this->tpl_data["create_item_form"] = $form;
		$this->tpl_data["return_uri"] = $return_uri;
	}
}
