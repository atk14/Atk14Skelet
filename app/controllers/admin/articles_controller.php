<?php
class ArticlesController extends AdminController{

	function index(){
		$this->page_title = _("Listing Articles");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());

		$conditions = $bind_ar = array();

		if($d["search"]){
			$_fields = array();
			$_fields[] = "id";
			foreach(array("title","body") as $_f){
				$_fields[] = "COALESCE((SELECT body FROM translations WHERE record_id=articles.id AND table_name='articles' AND key='$_f' AND lang=:lang),'')";
			}

			if($ft_cond = FullTextSearchQueryLike::GetQuery("UPPER(".join("||' '||",$_fields).")",Translate::Upper($d["search"]),$bind_ar)){
				$conditions[] = $ft_cond;
			}
		}

		$this->sorting->add("published_at",array("reverse" => true));
		$this->sorting->add("id");
		$this->sorting->add("title","UPPER(COALESCE((SELECT body FROM translations WHERE record_id=articles.id AND table_name='articles' AND key='title' AND lang=:lang),''))");
		
		$bind_ar[":lang"] = $this->lang;

		$this->tpl_data["finder"] = Article::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"offset" => $this->params->getInt("offset"),
			"order_by" => $this->sorting,
		));
	}

	function create_new(){
		$this->page_title = _("Create article");

		$this->_save_return_uri();

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$tags = $d["tags"];
			unset($d["tags"]);

			$article = Article::CreateNewRecord($d);
			$article->setTags($tags);

			$this->flash->success(_("The article has been created successfully"));
			$this->_redirect_back();
		}
	}

	function edit(){
		$this->page_title = sprintf(_("Editing %s"),$this->article);

		$this->_save_return_uri();
		$this->form->set_initial($this->article);
		$this->form->set_initial("tags",$this->article->getTags());

		if($this->request->post() && ($d = $this->form->validate($this->params))){

			if($d!=$this->form->get_initial()){
				$this->article->setTags($d["tags"]);
				unset($d["tags"]);
				$this->article->s($d,array("reconstruct_missing_slugs" => true));
				$this->flash->success(_("The article has been updated successfully"));
			}

			if($this->params->defined("save_and_stay")){
				if(!$this->request->xhr()){ $this->_redirect_to($this->request->getRequestUri()); }
				return;
			}

			$this->_redirect_back();
		}
	}

	function destroy(){
		$this->_destroy($this->article);
	}

	function _before_filter(){
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("article");
		}
	}
}
