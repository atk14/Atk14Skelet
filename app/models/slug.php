<?php
definedef("SLUG_MAX_LENGTH",200);

class Slug extends ApplicationModel{

	protected static $CACHE = array();
	protected static $CACHE_WITHOUT_SEGMENT = array();
	protected static $CACHE_SLUGS = array();

	 /**
	  *
	 	* Tato metoda musi byt! ApplicationModel ma totiz svuj jiny getSlug()
		* Parametry jsou tady pro dodrzeni kompatibility s ApplicationModel::getSlug(). Uf
		*/
	function getSlug($lang = null,$segment = ''){ return $this->g("slug"); }
	
	/**
	 *
	 *	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation"); // 123
	 *
	 *	// language detection
	 *	$lang = null;
	 *	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang); // 123
	 *	echo $lang; // "en"
	 *
	 *	$lang = "en";
	 *	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang); // 123
	 *
	 *	// invalid language passed
	 *	$lang = "cs";
	 *	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang); // null
	 *
	 *	// finding id in context of a specific segment
	 *	$lang = null;
	 * 	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang,["segment" => ""]); // 123
	 *	// or
	 * 	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang,""); // 123
	 *
	 *	// finding id regardless of a segment
	 *	$lang = null;
	 *	$segment = null;
	 * 	$id = Slug::GetRecordIdBySlug("articles","on-the-future-of-innovation",$lang,["consider_segment" => false]); // 123
	 */
	static function GetRecordIdBySlug($table_name,$slug,&$lang = null,$segment = '',$options = array()){
		global $ATK14_GLOBAL;

		if(is_array($segment)){
			$options = $segment;
			$segment = '';
		}

		$options += array(
			"segment" => $segment,
			"consider_segment" => true,
		);

		$segment = $options["consider_segment"] ? (string)$options["segment"] : null;

		$table_name_sluggish = Slug::StringToSluggish($table_name);
		// matching the generic form
		if(preg_match("/$table_name_sluggish-([a-z]{2})-(\d+)$/",$slug,$matches)){
			if(!$lang || $lang==$matches[1]){
				$lang = $matches[1];
				return (int)$matches[2];
			}
		}

		$cache_key = "$table_name" . (isset($segment) ? ",$segment" : "");
		
		if(!isset(Slug::$CACHE_SLUGS[$cache_key])){
			Slug::$CACHE_SLUGS[$cache_key] = array();
		}

		if(!isset(Slug::$CACHE_SLUGS[$cache_key]["$slug"])){
			//echo "<font color='green'>$table_name ($segment)...$slug</font><br>";
			$dbmole = Slug::GetDbmole();
			$slugs = isset($segment) ? $dbmole->selectIntoArray("SELECT slug FROM slugs WHERE table_name=:table_name AND segment=:segment LIMIT 100",[":table_name" => $table_name, ":segment" => $segment]) : $dbmole->selectIntoArray("SELECT slug FROM slugs WHERE table_name=:table_name LIMIT 1000",[":table_name" => $table_name]);
			$slugs[] = $slug;
			$slugs = array_unique($slugs);
			$slugs_to_read = [];
			foreach($slugs as $s){
				if(!isset(Slug::$CACHE_SLUGS[$cache_key]["$s"])){
					Slug::$CACHE_SLUGS[$cache_key]["$s"] = array();
					$slugs_to_read[] = $s;
				}
			}
			$rows = $dbmole->selectRows("SELECT record_id,slug FROM slugs WHERE table_name=:table_name AND slug IN :slgs",[":table_name" => $table_name, ":slgs" => $slugs_to_read]);
			foreach($rows as $row){
				Slug::$CACHE_SLUGS[$cache_key][$row["slug"]][] = $row["record_id"];
			}
		}
		Slug::_ReadCache($table_name,Slug::$CACHE_SLUGS[$cache_key]["$slug"],$segment);

		if($options["consider_segment"]){
			$CACHE = &Slug::$CACHE["$table_name,$segment"];
		}else{
			$CACHE = &Slug::$CACHE_WITHOUT_SEGMENT["$table_name"];
		}

		if($lang){
			if(isset($CACHE) && isset($CACHE[$lang]) && is_int($id = array_search($slug,$CACHE[$lang]))){
				return $id;
			}
		}else{
			foreach($CACHE as $l => $ary){
				if(is_int($id = array_search($slug,$ary))){
					$lang = $l;
					return $id;
				}
			}
		}

		return;
	}

	/**
	 * 
	 */
	static function SetObjectSlug($obj,$slug,$lang = null,$segment = '',$options = array()){
		global $ATK14_GLOBAL;

		if(!$lang){ $lang = $ATK14_GLOBAL->getDefaultLang(); } // !! default language
		$segment = (string)$segment;
		$options += array(
			"reconstruct_missing_slugs" => false,
		);

		$table_name = $obj->getTableName();
		$id = $obj->getId();

		if(self::_IsGenericSlug($slug,$obj,$lang)){ $slug = ""; } // pozor! toto je genericky slug, ten nebudeme ukladat

		if(!$slug && $options["reconstruct_missing_slugs"]){
			$slug = self::_BuildSlug($obj,$lang);
		}

		unset(Slug::$CACHE["$table_name,$segment"]);
		unset(Slug::$CACHE_WITHOUT_SEGMENT["$table_name"]);
		Slug::$CACHE_SLUGS = array();

		$values = array(
			"table_name" => $table_name,
			"record_id" => $id,
			"lang" => $lang,
			//"segment" => $segment,
		);
		$slug_obj = Slug::FindFirst(array("conditions" => $values)); // hledame bez segmentu - ten totiz muze byt zmenen po aktualizaci objektu ($object->setValues())

		$values["segment"] = $segment;

		if(!$slug){
			$slug_obj && $slug_obj->destroy(); // mazeme
			return;
		}

		if($slug_obj){
			// aktualizujeme pouze, pokud se neco skutecne meni...
			if($slug!==$slug_obj->g("slug") || $segment!==$slug_obj->g("segment")){
				$slug_obj->s(array(
					"slug" => $slug,
					"segment" => $segment,
				));
			}
			return;
		}

		$values["slug"] = $slug;
		Slug::CreateNewRecord($values);
	}

	static function GetObjectSlug($obj,$lang = null,$segment = ''){
		global $ATK14_GLOBAL;
		if(!$lang){ $lang = $ATK14_GLOBAL->getLang(); } // !! current language

		$table_name = $obj->getTableName();
		$id = $obj->getId();
		$class_name = get_class($obj);

		$record_ids = Cache::CachedIds($class_name);
		if(!in_array($id,$record_ids)){
			$record_ids[] = $id;
		}
		Slug::_ReadCache($table_name,$record_ids,$segment);

		if(isset(Slug::$CACHE["$table_name,$segment"][$lang][$id])){
			return Slug::$CACHE["$table_name,$segment"][$lang][$id];
		}

		$table_name_sluggish = Slug::StringToSluggish($table_name);
		return "$table_name_sluggish-$lang-$id";
	}

	static function _ReadCache($table_name,$record_ids,$segment = '',$force_read = false){
		global $ATK14_GLOBAL;

		if(is_null($segment)){
			return self::_ReadCacheWithoutSegment($table_name,$record_ids,$force_read);
		}

		$languages = $ATK14_GLOBAL->getSupportedLangs();
		$def_language = $languages[0];

		$segment = (string)$segment;

		if($force_read){
			Slug::$CACHE = array();
		}

		if(!isset(Slug::$CACHE["$table_name,$segment"])){
			Slug::$CACHE["$table_name,$segment"] = array();
			foreach($languages as $l){
				Slug::$CACHE["$table_name,$segment"][$l] = array();
			}
		}
		$CACHE = &Slug::$CACHE["$table_name,$segment"];

		$ids_to_read = array();
		foreach($record_ids as $id){
			if(!array_key_exists($id,$CACHE[$def_language])){
				$ids_to_read[] = $id;
				foreach($languages as $l){
					$CACHE[$l][$id] = null;
				}
			}
		}

		if(!$ids_to_read){
			return;
		}

		$dbmole = Slug::GetDbmole();
		$rows = $dbmole->selectRows("SELECT lang,record_id,slug FROM slugs WHERE table_name=:table_name AND segment=:segment AND record_id IN :ids",array(":table_name" => $table_name,":segment" => $segment,":ids" => $ids_to_read));
		foreach($rows as $row){
			$l = $row["lang"];
			$id = (int)$row["record_id"];
			$CACHE[$l][$id] = $row["slug"];
		}
	}

	static function _ReadCacheWithoutSegment($table_name,$record_ids,$force_read = false){
		global $ATK14_GLOBAL;

		$languages = $ATK14_GLOBAL->getSupportedLangs();
		$def_language = $languages[0];

		if($force_read){
			Slug::$CACHE_WITHOUT_SEGMENT = array();
		}

		if(!isset(Slug::$CACHE_WITHOUT_SEGMENT["$table_name"])){
			Slug::$CACHE_WITHOUT_SEGMENT["$table_name"] = array();
			foreach($languages as $l){
				Slug::$CACHE_WITHOUT_SEGMENT["$table_name"][$l] = array();
			}
		}
		$CACHE_WITHOUT_SEGMENT = &Slug::$CACHE_WITHOUT_SEGMENT["$table_name"];

		$ids_to_read = array();
		foreach($record_ids as $id){
			if(!array_key_exists($id,$CACHE_WITHOUT_SEGMENT[$def_language])){
				$ids_to_read[] = $id;
				foreach($languages as $l){
					$CACHE_WITHOUT_SEGMENT[$l][$id] = null;
				}
			}
		}

		if(!$ids_to_read){
			return;
		}

		$dbmole = Slug::GetDbmole();
		$rows = $dbmole->selectRows("SELECT lang,record_id,slug FROM slugs WHERE table_name=:table_name AND record_id IN :ids",array(":table_name" => $table_name,":ids" => $ids_to_read));
		foreach($rows as $row){
			$l = $row["lang"];
			$id = (int)$row["record_id"];
			$CACHE_WITHOUT_SEGMENT[$l][$id] = $row["slug"];
		}
	}

	static function DeleteObjectSlugs($obj){
		$table_name = $obj->getTableName();
		$record_id = $obj->getId();
		$dbmole = Slug::GetDbmole();
		$dbmole->doQuery("DELETE FROM slugs WHERE table_name=:table_name AND record_id=:record_id",array(
			":table_name" => $table_name,
			":record_id" => $record_id,
		));
		Slug::$CACHE = array(); // mazeme uplne vsechno...
		Slug::$CACHE_WITHOUT_SEGMENT = array();
		Slug::$CACHE_SLUGS = array();
	}

	static function StringToSluggish($string,$suffix = "") {
		// just checking whether there is a proper version of String4
		myAssert(String4::ToObject("test 123")->toSlug(array("suffix" => "456"))->toString() === "test-123-456");

		$_sluggish = new String4($string);
		return $_sluggish->toSlug(array("max_length" => SLUG_MAX_LENGTH, "suffix" => $suffix));
	}

	/**
	 * Ulozi chybejici slugy daneho objektu.
	 */
	static function ComplementSlugs($object){
		$id = $object->getId();
		$table_name_sluggish = Slug::StringToSluggish($object->getTableName());
		$class_name = get_class($object);

		foreach($GLOBALS["ATK14_GLOBAL"]->getSupportedLangs() as $lang){
			$current_slug = $object->getSlug($lang);
			
			if($current_slug!="$table_name_sluggish-$lang-$id"){
				// nema genericky slug? - preskakujeme to
				continue;
			}

			$slug = self::_BuildSlug($object,$lang);
			if(!$slug){
				continue;
			}

			$object->setSlug($slug,$lang,$object->getSlugSegment());
		}
	}

	protected static function _BuildSlug($object,$lang){
		$class_name = get_class($object);

		if(!$pattern = $object->getSlugPattern($lang)){
			// ha, object sam nevi, podle ceho se ma slug vytvorit
			return;
		}

		$suffix = 2;
		$slug = Slug::StringToSluggish($pattern);
		while($class_name::GetInstanceBySlug($slug,$lang,$object->getSlugSegment())){
			if($suffix>=100){ throw new Exception("Slug::_BuildSlug(): Too many retries"); }

			$_suffix = $suffix<=50 ? $suffix : uniqid();
			$slug = Slug::StringToSluggish($pattern,$_suffix);

			$suffix++;
		}
		return $slug;
	}

	protected static function _IsGenericSlug($slug,$object,$lang){
		$table_name = $object->getTableName();
		$id = $object->getId();

		$tbl_name_sluggish = Slug::StringToSluggish($table_name);
		return $slug=="$tbl_name_sluggish-$lang-$id";
	}
}
