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
			$this->breadcrumbs[] = $tag->getTagLocalized();
			$this->page_title = sprintf(_("Articles tagged with <em>%s</em>"),h($tag->getTagLocalized()));
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
			"order_by" => "published_at DESC, id DESC",
			"offset" => $this->params->getInt("offset")
		));
		$this->head_tags->setCanonical($this->_build_canonical_url("articles/index"));
	}

	function detail(){
		$article = $this->_just_find("article");
		if(!$article || (!$article->isPublished() && !($this->logged_user && $this->logged_user->isAdmin()))){
			return $this->_execute_action("error404");
		}

		$this->page_title = $article->getPageTitle();
		$this->page_description = $article->getPageDescription();

		$this->tpl_data["article"] = $article;
		$this->tpl_data["tags"] = $article->getTags();

		$this->tpl_data["older_article"] = $article->getOlderArticle();
		$this->tpl_data["newer_article"] = $article->getNewerArticle();

		if($primary_tag = $article->getPrimaryTag()){
			$this->breadcrumbs[] = array($primary_tag->getTagLocalized(),$this->_link_to(array("action" => "articles/index", "tag_id" => $primary_tag)));
		}
		$this->breadcrumbs[] = $article->getTitle();
		$this->head_tags->setCanonical($this->_build_canonical_url(["action" => "articles/detail", "id" => $article]));
	}

	function _before_filter(){
		$this->breadcrumbs[] = array(_("Articles"),$this->_link_to("articles/index"));
	}
}
