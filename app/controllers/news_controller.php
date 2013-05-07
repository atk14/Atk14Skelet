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

		$this->tpl_data["older_news"] = $news->getOlderNews();
		$this->tpl_data["newer_news"] = $news->getNewerNews();
	}
}
