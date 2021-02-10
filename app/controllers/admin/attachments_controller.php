<?php
class AttachmentsController extends AdminController{

	function index(){
		$this->page_title = strlen($this->section) ? sprintf(_("Attachments for object %s#%s, section %s"),$this->table_name,$this->record_id,$this->section) : sprintf(_("Attachments for object %s#%s"),$this->table_name,$this->record_id);
		$this->tpl_data["attachments"] = Attachment::FindAll("table_name",$this->table_name,"record_id",$this->record_id,"section",$this->section);
	}

	function create_new(){
		$this->page_title = _("Adding attachment");

		$this->_save_return_uri();
		if($this->request->post() && ($d = $this->form->validate($this->params))){

			$attachment = $d["url"];

			$d["table_name"] = $this->table_name;
			$d["record_id"] = $this->record_id;
			$d["section"] = $this->section;

			$d["url"] = $attachment->getUrl();

			Attachment::CreateNewRecord($d);

			$this->flash->success(_("Attachment has been saved"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$this->page_title = sprintf(_("Editing attachment #%s"),$this->attachment->getId());

		$this->_save_return_uri();
		$this->form->set_initial($this->attachment);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->attachment->s($d);
			$this->flash->success(_("Changes have been saved"));
			$this->_redirect_back();
		}
	}

	function destroy(){
		if(!$this->request->post()){
			return $this->_execute_action("error404");
		}

		$this->attachment->destroy();

		if(!$this->request->xhr()){
			$this->flash->success(_("Attachment has been deleted"));
			$this->_redirect_back();
		}
	}

	function set_rank(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }

		$this->render_template = false;
		$this->attachment->setRank($this->params->getInt("rank"));
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
			$this->_find("attachment");
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
}
