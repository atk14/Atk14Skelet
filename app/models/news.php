<?php
class News extends ApplicationModel{
	function isPublished(){
		return strtotime($this->getPublishedAt())<time();
	}

	function getAuthor(){ return User::GetInstanceById($this->getAuthorId()); }

	function getNewerNews(){
		return News::FindFirst(array(
			"conditions" => "published_at>:published_at AND published_at<NOW()",
			"bind_ar" => array(":published_at" => $this->getPublishedAt()),
			"order_by" => "published_at",
		));
	}

	function getOlderNews(){
		return News::FindFirst(array(
			"conditions" => "published_at<:published_at AND published_at<NOW()",
			"bind_ar" => array(":published_at" => $this->getPublishedAt()),
			"order_by" => "published_at DESC",
		));
	}
}
