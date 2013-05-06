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

		$news_navi = new Navigation();

		$news_navi->addHeader(_("News..."));

		if($older = $news->getOlderNews()){
			$news_navi->add(sprintf(_("Older news: %s"),$older->getTitle()),array("action" => "detail", "id" => $older));
		}
		if($newer = $news->getNewerNews()){
			$news_navi->add(sprintf(_("Newer news: %s"),$newer->getTitle()),array("action" => "detail", "id" => $newer));
		}
		$news_navi->addDivider();
		$news_navi->add(_("News archive"),"index");

		$this->tpl_data["news_navi"] = $news_navi;
	}
}
