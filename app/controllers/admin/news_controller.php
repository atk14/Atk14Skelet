<?php
class NewsController extends AdminController{
	function index(){
		$this->page_title = _("Listing News");

		$this->sorting->add("created_at",array("reverse" => true));

		$this->tpl_data["finder"] = News::Finder(array(
			"offset" => $this->params->getInt("from"),
			"order_by" => $this->sorting
		));

		$this->context_menu->add(_("Create new entry"),"create_new");
	}

	function create_new(){
		$this->page_title = _("Create news entry");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["author_id"] = $this->logged_user;
			News::CreateNewRecord($d);
			$this->flash->success(_("The news entry has been created successfuly"));
			$this->_redirect_back();	
		}
	}

	function edit(){
		$this->page_title = sprintf(_("Editing %s"),$this->news);

		$this->_save_return_uri();
		$this->form->set_initial($this->news);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->news->s($d);
			$this->flash->success(_("The news entry has been updated successfuly"));
			$this->_redirect_back();	
		}
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("news");
		}
	}
}
