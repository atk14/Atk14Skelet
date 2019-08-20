<?php
class Tag extends ApplicationModel{
	const ID_NEWS = 1; // see db/migrations/0004_tags.sql

	/**
	 * $tag = Tag::GetOrCreateTag("Nikon");
	 */
	static function GetOrCreateTag($tag){
		if(!strlen($tag)){ return null; }

		($out = Tag::FindByTag($tag)) ||
		($out = Tag::CreateNewRecord(array("tag" => $tag)));

		return $out;
	}

	function toString(){ return $this->getTag(); }

	function isDeletable(){
		return
			$this->getId()!=static::ID_NEWS &&
			0==($this->dbmole->selectInt("SELECT COUNT(*) FROM article_tags WHERE tag_id=:id",array(":id" => $this)));
	}
}
