<?php
class TagsController extends AdminController{
	function index(){
		$this->page_title = _("Listing Tags");

		$this->sorting->add("tag","LOWER(tag)");
		$this->sorting->add("created_at",array("reverse" => true));

		$this->tpl_data["finder"] = Tag::Finder(array(
			"order_by" => $this->sorting
		));
	}

	function create_new(){
		$this->page_title = _("Create a new tag");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["created_by_user_id"] = $this->logged_user;
			Tag::CreateNewRecord($d);
			$this->flash->success(_("The tag has been created successfuly"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$tag = $this->tag;

		$this->page_title = sprintf(_("Editing tag %s"),$tag);

		$this->form->set_initial($tag);
		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(Tag::FindFirst("tag=:tag AND id!=:id",array(":tag" => $d["tag"], ":id" => $tag))){
				$this->form->set_error("tag",_("The same tag already exists"));
				return;
			}

			if($d!=$this->form->get_initial()){
				$d["updated_by_user_id"] = $this->logged_user;
				$tag->s($d);
				$this->flash->success(_("The tag has been updated"));
			}else{
				$this->flash->notice(_("Nothing has been changed"));
			}
			$this->_redirect_back();
		}
	}

	function destroy(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }
		if(!$this->tag->isDeletable()){ return $this->_execute_action("error403"); }

		$this->tag->destroy();
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("tag");
		}
	}
}	
