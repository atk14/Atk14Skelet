<?php
class TagsController extends AdminController{

	function index(){
		$this->page_title = _("Listing Tags");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$_fields = array();
			$_fields[] = "id";
			$_fields[] = "tag";
			$_fields[] = "COALESCE(code,'')";
			$_fields[] = "COALESCE((SELECT body FROM translations WHERE record_id=tags.id AND table_name='tags' AND key='tag_localized' AND lang=:lang),'')";
			if($ft_cond = FullTextSearchQueryLike::GetQuery("LOWER(".join("||' '||",$_fields).")",Translate::Lower($d["search"]),$bind_ar)){
				$conditions[] = $ft_cond;
				$bind_ar[":lang"] = $this->lang;
			}

			$this->sorting->add("default","LOWER(tag) LIKE :search||'%' DESC, id||''=:search DESC, LOWER(tag)"); // in searching the default ordering is more usefull: exact match with id or at the beginning of a tag is prioritized
			$bind_ar[":search"] = Translate::Lower($d["search"]);
		}

		$this->sorting->add("created_at",array("reverse" => true));
		$this->sorting->add("tag","LOWER(tag)");
		$this->sorting->add("code","code IS NULL, code, LOWER(tag)","code IS NULL, code DESC, LOWER(tag)");
		$this->sorting->add("id");

		$this->tpl_data["finder"] = Tag::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}

	function create_new(){
		$this->page_title = _("Create a new tag");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$d["created_by_user_id"] = $this->logged_user;
			Tag::CreateNewRecord($d);
			$this->flash->success(_("The tag has been created successfully"));
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

			if($d==$this->form->get_initial()){
				$this->flash->notice(_("Nothing has been changed"));
				return $this->_redirect_back();
			}

			$d["updated_by_user_id"] = $this->logged_user;
			$tag->s($d);
			$this->flash->success(_("The tag has been updated"));
			$this->_redirect_back();
		}
	}

	function destroy(){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }
		if(!$this->tag->isDeletable()){ return $this->_execute_action("error403"); }

		$this->_destroy($this->tag);
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("tag");
		}
	}
}
