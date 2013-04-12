<?php
class NewsController extends ApplicationController{

	function index(){
		$this->page_title = _("News");
		$this->tpl_data["finder"] = News::Finder(array(
			"conditions" => "published_at<NOW()",
			"order_by" => "published_at DESC",
			"offset" => $this->params->getInt("offset")
		));
	}

	function detail(){
		$news = News::FindById($this->params->getInt("id"));
		if(!$news || !$news->isPublished()){
			return $this->_execute_action("error404");
		}

		$this->page_title = $news->getTitle();
		$this->tpl_data["news"] = $news;

		if($older = $news->getOlderNews()){
			$this->context_menu->add(sprintf(_("Older news: %s"),$older->getTitle()),array("action" => "detail", "id" => $older));
		}
		if($newer = $news->getNewerNews()){
			$this->context_menu->add(sprintf(_("Newer news: %s"),$newer->getTitle()),array("action" => "detail", "id" => $newer));
		}
		$this->context_menu->add(_("News archive"),"index");
	}
}
