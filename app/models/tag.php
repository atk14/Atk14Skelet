<?php
class Tag extends ApplicationModel{
	const ID_NEWS = 1; // see db/migrations/0004_tags.sql

	function toString(){ return $this->getTag(); }

	function isDeletable(){
		return
			$this->getId()!=static::ID_NEWS &&
			0==($this->dbmole->selectInt("SELECT COUNT(*) FROM article_tags WHERE tag_id=:id",array(":id" => $this)));
	}
}
