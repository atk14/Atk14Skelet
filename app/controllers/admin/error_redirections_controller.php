<?php
class ErrorRedirectionsController extends AdminController {

	function index(){
		$this->page_title = _("404 Redirections");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$_fields = array();
			$_fields[] = "id";
			$_fields[] = "source_url";
			$_fields[] = "target_url";

			if($ft_cond = FullTextSearchQueryLike::GetQuery("LOWER(".join("||' '||",$_fields).")",Translate::Lower($d["search"]),$bind_ar)){
				$conditions[] = $ft_cond;
			}

			$this->sorting->add("default","id||''=:search DESC, LOWER(source_url) LIKE :search||'%' DESC, LOWER(target_url) LIKE :search||'%' DESC, last_accessed_at IS NOT NULL DESC, last_accessed_at DESC, created_at DESC"); // default ordering is tuned in searching
			$bind_ar[":search"] = Translate::Lower($d["search"]);
		}

		$this->sorting->add("last_accessed_at","last_accessed_at IS NOT NULL DESC, last_accessed_at DESC, created_at DESC","last_accessed_at IS NOT NULL DESC, last_accessed_at ASC, created_at ASC");
		$this->sorting->add("created_at",array("reverse" => true));
		$this->sorting->add("id");
		$this->sorting->add("target_url");
		$this->sorting->add("source_url");

		$this->tpl_data["finder"] = ErrorRedirection::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"offset" => $this->params->getInt("offset"),
			"order_by" => $this->sorting,
		));
	}

	function create_new(){
		$this->page_title = _("Create redirection");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(ErrorRedirection::FindFirst("source_url",$d["source_url"])){
				$this->form->set_error("source_url",_("Another redirection with the same source URL already exists"));
				return;
			}
			ErrorRedirection::CreateNewRecord($d);
			ErrorRedirection::RefreshCache();
			$this->flash->success(_("The redirection has been created successfully"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$this->page_title = _("Editing redirection");

		$this->_save_return_uri();
		$this->form->set_initial($this->error_redirection);

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(ErrorRedirection::FindFirst("source_url=:source_url AND id!=:id",array(":source_url" => $d["source_url"], ":id" => $this->error_redirection))){
				$this->form->set_error("source_url",_("Another redirection with the same source URL already exists"));
				return;
			}
			$this->error_redirection->s($d);
			ErrorRedirection::RefreshCache();
			$this->flash->success(_("The redirection has been updated successfully"));
			$this->_redirect_back();
		}
	}

	function destroy(){
		$this->_destroy();
		ErrorRedirection::RefreshCache();
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("error_redirection");
		}
	}
}
