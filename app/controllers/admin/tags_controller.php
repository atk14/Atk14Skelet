<?php
class TagsController extends AdminController{
	function index(){
		$this->page_title = _("Listing Tags");

		$this->sorting->add("tag","LOWER(tag)");

		$this->tpl_data["finder"] = Tag::Finder(array(
			"order_by" => $this->sorting
		));
	}

	function create_new(){
		$this->page_title = _("Create a new tag");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(Tag::FindFirstByTag($d["tag"])){
				$this->form->set_error("tag",_("The same tag already exists"));
				return;
			}

			$d["created_by_user_id"] = $this->logged_user;
			Tag::CreateNewRecord($d);
			$this->flash->success(_("The tag has been created successfuly"));
			$this->_redirect_back();
		}
	}

	function edit(){
		if(!$tag = $this->_just_find("tag")){
			return $this->_execute_action("error404");
		}

		$this->page_title = _("Editing tag");

		$this->form->set_initial($tag);
		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(Tag::FindFirst("tag=:tag AND id!=:id",array(":tag" => $d["tag"], ":id" => $tag))){
				$this->form->set_error("tag",_("The same tag already exists"));
				return;
			}

			$d["updated_by_user_id"] = $this->logged_user;
			$tag->s($d);
			$this->flash->success(_("The tag has been updated"));
			$this->_redirect_back();
		}
	}
}
