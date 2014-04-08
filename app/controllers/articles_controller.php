<?php
class ArticlesController extends ApplicationController{
	function detail(){
		$article = $this->_just_find("article");
		if(!$article || !$article->isPublished()){
			return $this->_execute_action("error404");
		}
		if(in_array(Tag::ID_NEWS,$article->getTagsLister()->getRecordIds())){
			return $this->_redirect_to(array("action" => "news/detail", "id" => $article)); // this is an article for news section
		}

		$this->page_title = $article->getTitle();
		$this->tpl_data["article"] = $article;
	}
}
