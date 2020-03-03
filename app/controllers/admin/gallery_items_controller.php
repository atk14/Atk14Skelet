<?php
class GalleryItemsController extends AdminController{

	function create_new(){
		$gallery = $this->gallery;
		$this->_prepare_breadcrumbs($gallery);

		$this->_save_return_uri();
		$return_uri = $this->_get_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["gallery_id"] = $gallery;
			$item = GalleryItem::CreateNewRecord($d);

			if($this->request->xhr()){
				$this->render_template = false;
				$this->response->write(json_encode($this->_dump_image($item)));
				$this->response->setContentType("text/plain");
				return;
			}

			$this->_redirect_to($this->_link_to(array("action" => "galleries/detail", "id" => $gallery, "return_uri" => $return_uri)));
		}

		/*
		$this->_create_new(array(
			"create_closure" => function($d) use ($gallery){
				$d["gallery_id"] = $gallery;
				return GalleryItem::CreateNewRecord($d);
			},
			"redirect_to" => function($record) use($gallery,$return_uri){
				return Atk14Url::BuildLink(array("action" => "galleries/detail", "id" => $gallery,"return_uri" => $return_uri),array("connector" => "&"));
			}
		));
		*/
	}

	function edit(){
		$gallery = $this->gallery_item->getGallery();
		$this->_prepare_breadcrumbs($gallery);

		$this->_edit(array(
			"object" => $this->gallery_item,
		));
	}

	function set_rank(){
		$this->_set_rank();
	}

	function destroy(){
		$this->_destroy();
	}

	function _before_filter(){
		if(in_array($this->action,array("create_new"))){
			$this->_find("gallery","gallery_id");
		}

		if(in_array($this->action,array("destroy","edit"))){
			$this->_find("gallery_item");
		}
	}

	function _prepare_breadcrumbs($gallery){
		$this->_add_gallery_to_breadcrumbs($gallery,array(
			"url" => $this->_link_to(array("action" => "galleries/detail", "id" => $gallery, "return_uri" => $this->_get_return_uri())),
		));
	}

	function _dump_image($item){
		$pupiq = new Pupiq($item->getImageUrl());
		$pupiq->setGeometry("x60");
		return array(
			"id" => $item->getId(),
			"url" => $pupiq->getUrl(),
			"width" => $pupiq->getWidth(),
			"height" => $pupiq->getHeight(),
			"original_geometry" => array(
				"width" => $pupiq->getOriginalWidth(),
				"height" => $pupiq->getOriginalHeight(), 
			),
			"image_gallery_item" => $this->_render("galleries/gallery_item_item",array("gallery_item" => $item)),
		);
	}
}
