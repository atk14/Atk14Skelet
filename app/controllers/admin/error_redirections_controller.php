<?php
class ErrorRedirectionsController extends AdminController {

	function index(){
		$this->page_title = _("404 Redirections");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$conditions[] = "UPPER(id||' '||source_url||' '||target_url) LIKE UPPER('%'||:search||'%')";
			$bind_ar[":search"] = $d["search"];
		}

		$this->sorting->add("last_accessed_at","last_accessed_at IS NOT NULL DESC, last_accessed_at DESC, created_at DESC","last_accessed_at IS NOT NULL DESC, last_accessed_at ASC, created_at ASC");
		$this->sorting->add("id");

		$this->tpl_data["finder"] = ErrorRedirection::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"offset" => $this->params->getInt("offset"),
		));
	}

	function create_new(){
		$this->page_title = _("Create redirection");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$redirection = ErrorRedirection::CreateNewRecord($d);
			$this->flash->success(_("The redirection has been created successfully"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$this->page_title = _("Editing redirection");

		$this->_save_return_uri();
		$this->form->set_initial($this->redirection);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->redirection->s($d);
			$this->flash->success(_("The redirection has been updated successfully"));
			$this->_redirect_back();
		}
	}

	function destroy(){
		$this->_destroy();
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("redirection");
		}
	}
}
