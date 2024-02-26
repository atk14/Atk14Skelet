<?php
class ImagesController extends AdminController {

	function index(){
		$this->page_title = strlen($this->section) ? sprintf(_("Photo gallery of the object %s#%s, section %s"),$this->table_name,$this->record_id,$this->section) : sprintf(_("Photo gallery of the object %s#%s"),$this->table_name,$this->record_id);
		$class_name = $this->_get_class_name();
		$this->tpl_data["images"] = $class_name::FindAll("table_name",$this->table_name,"record_id",$this->record_id,"section",$this->section);
	}

	function create_new(){
		$this->page_title = _("Adding an image into the photo gallery");

		if($this->_get_class_name()=="ProductImage"){
			$this->form->tune_for_product_image();
			if($this->request->xhr() && !$this->params->defined("display_on_card")){
				// HACK: during a XHR request the display_on_card checkbox is not shown in the upload form, it should be checked by default
				$this->params["display_on_card"] = "on";
			}
		}

		$this->_save_return_uri();
		if($this->request->post()){
			if($d = $this->form->validate($this->params)){
				$pupiq = $d["url"];

				$d["table_name"] = $this->table_name;
				$d["record_id"] = $this->record_id;
				$d["url"] = $pupiq->getUrl();
				$d["section"] = $this->section;

				$class_name = $this->_get_class_name();
				$image = $class_name::CreateNewRecord($d);

				if($this->request->xhr()){
					$this->render_template = false;
					$this->response->write(json_encode($this->_dump_image($image)));
					$this->response->setContentType("text/plain");
					return;
				}

				$this->flash->success(_("Image has been created"));
				$this->_redirect_back();
			}else{
				if($this->request->xhr()){
					$this->response->setStatusCode(400); // "Bad Request"
					$this->render_template = false;
					$this->response->write(json_encode($this->_dump_form_error_message($this->form)));
					$this->response->setContentType("text/plain");
					return;
				}
			}
		}
	}

	function edit(){
		$this->page_title = sprintf(_("Editing of the Image #%s"),$this->image->getId());

		if($this->_get_class_name()=="ProductImage"){
			$this->form->tune_for_product_image();
		}

		$this->_save_return_uri();
		$this->form->set_initial($this->image);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->image->s($d);

			$this->flash->success(_("Changes have been saved"));
			$this->_redirect_back();
		}
	}

	function destroy(){
		if(!$this->request->post()){
			return $this->_execute_action("error404");
		}

		$this->image->destroy();

		if(!$this->request->xhr()){
			$this->flash->success(_("The image has been deleted"));
			$this->_redirect_back();
		}
	}

	function set_rank(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }

		$this->render_template = false;
		$this->image->setRank($this->params->getInt("rank"));
	}

	function _before_filter(){
		if(in_array($this->action,array("index","create_new"))){
			// table_name and record_id are mandatory parameters
			if (
				!($this->table_name = $this->tpl_data["table_name"] = (string)$this->params->getString("table_name")) ||
				!($this->record_id = $this->tpl_data["record_id"] = (int)$this->params->getInt("record_id"))
			){
				return $this->_execute_action("error404");
			}
			// section is optional parameter
			$this->section = $this->tpl_data["section"] = (string)$this->params->getString("section");
		}

		if(in_array($this->action,array("destroy","set_rank","edit"))){
			if(
				!(class_exists("ProductImage") && ($image = $this->_just_find("image",array("class_name" => "ProductImage")))) &&
				!($image = $this->_just_find("image"))
			){
				return $this->_execute_action("error404");
			}
			$this->image = $this->tpl_data["image"] = $image;
			$this->table_name = $image->g("table_name");
			$this->section = $image->g("section");
			$this->record_id = $image->getRecordId();
		}
	}

	function _redirect_back($default = null){
		if(!$default){
			$default = array(
				"action" => "index",
				"table_name" => $this->table_name,
				"record_id" => $this->record_id,
			);
		}
		return parent::_redirect_back($default);
	}

	function _get_class_name(){
		return $this->table_name=="products" ? "ProductImage" : "Image";
	}

	function _dump_image($image){
		$pupiq = new Pupiq($image->getUrl());
		$pupiq->setGeometry("x60");
		return array(
			"id" => $image->getId(),
			"url" => $pupiq->getUrl(),
			"width" => $pupiq->getWidth(),
			"height" => $pupiq->getHeight(),
			"original_geometry" => array(
				"width" => $pupiq->getOriginalWidth(),
				"height" => $pupiq->getOriginalHeight(), 
			),
			"image_gallery_item" => $this->_render("shared/image_gallery_item",array("image" => $image)),
		);
	}

	function _dump_form_error_message($form){
		$ar = $form->get_errors();

		$out = array();
		foreach($ar as $k => $_ar){
			foreach($_ar as $err){
				$out[] = $err;
			}
		}

		return array(
			"error_message" => join("; ",$out),
		);
	}
}
