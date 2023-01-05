<?php
class Tag extends ApplicationModel implements Translatable {

	use TraitGetInstanceByCode;

	static function GetTranslatableFields(){ return ["tag_localized"]; }

	/**
	 * $tag = Tag::GetOrCreateTag("Nikon");
	 */
	static function GetOrCreateTag($tag){
		if(!strlen((string)$tag)){ return null; }

		($out = Tag::FindByTag($tag)) ||
		($out = Tag::CreateNewRecord(array("tag" => $tag)));

		return $out;
	}

	function getTagLocalized($lang = null){
		global $ATK14_GLOBAL;

		if(!$lang){
			$lang = $ATK14_GLOBAL->getLang();
		}

		if(strlen($out = (string)$this->g("tag_localized_$lang"))>0){
			return $out;
		}

		return $this->getTag();
	}

	function toString(){ return $this->getTag(); }

	function isDeletable(){
		return
			is_null($this->getCode()) &&
			0==($this->dbmole->selectInt("
				SELECT SUM(cnt) FROM (
					SELECT COUNT(*) AS cnt FROM article_tags WHERE tag_id=:id UNION
					-- here is a place for other queries
					SELECT 0 AS cnt
				)q
			",array(":id" => $this)));
	}
}
