<?php
class ArticlesController extends ApplicationController{

	function index(){
		$tag = null;
		if($this->params->defined("tag_id") && !($tag = $this->_just_find("tag","tag_id"))){
			$this->_execute_action("error404");
			return;
		}

		$this->page_title = _("Articles");

		if($tag){
			$this->breadcrumbs[] = "$tag";
			$this->page_title = sprintf(_("Articles tagged with <em>%s</em>"),h($tag));
		}

		$conditions = $bind_ar = array();

		$conditions[] = "published_at<:now";
		$bind_ar[":now"] = now();

		if($tag){
			$conditions[] = "id IN (SELECT article_id FROM article_tags WHERE tag_id=:tag)";
			$bind_ar[":tag"] = $tag;
		}

		$this->tpl_data["finder"] = Article::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => "published_at DESC",
			"offset" => $this->params->getInt("offset")
		));
	}

	function detail(){
		$article = $this->_just_find("article");
		if(!$article || !$article->isPublished()){
			return $this->_execute_action("error404");
		}

		$this->tpl_data["tags"] = $article->getTags();

		$this->page_title = $article->getTitle();
		$this->tpl_data["article"] = $article;

		$this->page_title = $article->getTitle();
		$this->tpl_data["article"] = $article;

		$this->tpl_data["older_article"] = $article->getOlderArticle();
		$this->tpl_data["newer_article"] = $article->getNewerArticle();

		if($primary_tag = $article->getPrimaryTag()){
			$this->breadcrumbs[] = array("$primary_tag",$this->_link_to(array("action" => "articles/index", "tag_id" => $primary_tag)));
		}
		$this->breadcrumbs[] = $article->getTitle();
	}

	function _before_filter(){
		$this->breadcrumbs[] = array(_("Articles"),$this->_link_to("articles/index"));
	}
}
