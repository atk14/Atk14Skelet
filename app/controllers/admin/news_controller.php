<?php
class NewsController extends AdminController{
	function index(){
		$this->page_title = _("Listing News");

		$this->sorting->add("created_at",array("reverse" => true));

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$conditions[] = "UPPER(id||' '||' '||COALESCE(title,'')||' '||COALESCE(body,'')) LIKE UPPER('%'||:search||'%')";
			$bind_ar[":search"] = $d["search"];
		}

		$this->tpl_data["finder"] = News::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"offset" => $this->params->getInt("from"),
			"order_by" => $this->sorting,
		));
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

	function destroy(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }

		$this->news->destroy();

		if(!$this->request->xhr()){
			$this->flash->success(_("The news entry has been deleted"));
			$this->_redirect_to("index");
		}
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("news");
		}
	}
}
