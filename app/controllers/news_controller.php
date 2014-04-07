<?php
class NewsController extends ApplicationController{

	const TAG_ID = Tag::ID_NEWS;

	function index(){
		$this->page_title = _("News");
		$this->tpl_data["finder"] = Article::Finder(array(
			"conditions" => "published_at<NOW() AND id IN (SELECT article_id FROM article_tags WHERE tag_id=:tag_id)",
			"bind_ar" => array(":tag_id" => static::TAG_ID),
			"order_by" => "published_at DESC",
			"offset" => $this->params->getInt("offset")
		));
	}

	function detail(){
		$news = Article::FindById($this->params->getInt("id"));
		if(!$news || !$news->isPublished() || !in_array(static::TAG_ID,$news->getTagsLister()->getRecordIds())){
			return $this->_execute_action("error404");
		}

		$this->page_title = $news->getTitle();
		$this->tpl_data["news"] = $news;

		$this->tpl_data["older_news"] = $news->getOlderArticle(static::TAG_ID);
		$this->tpl_data["newer_news"] = $news->getNewerArticle(static::TAG_ID);
	}
}
