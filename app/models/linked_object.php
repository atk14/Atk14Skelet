<?php

/**
 *
 * Neco jako proxy trida nebo trait, ktera zajisti aby se model choval jako pripojitelny objekt.
 * Zaroven se porad bude chovat jako ApplicationModel
 *
 * Takovyto model musi obsahovat db pole table_name, record_id a rank
 *
 * Napr. chceme, aby se obrazky daly pripojit k produktu, ke clanku atd.
 * Trida Image bude dedit LinkedObject
 * ```
 * class Image extends LinkedObject {
 * }
 * ```
 * Objekty Image budou pripojene k produktu, takze images.table_name bude obsahovat 'products'.
 * Metody z tridy LinkedObject nam potom umozni pouzivat metodu Image::GetInstancesFor($product);
 *
 * @note prevzato z jineho projektu, puvodne to byl AnyAttachmentModel, prejmenoval jsem pro lepsi orientaci
 */
class LinkedObject extends ApplicationModel implements Rankable {

	static $CACHE = array();

	static function CreateNewRecord($values,$options = array()){
		static::_ClearCache($values["table_name"],$values["record_id"]);
		return parent::CreateNewRecord($values,$options);
	}

	/**
	 *
	 *	$image = Image::CreateNewRecord($article,["url" => "..."]);
	 *	$image = Image::CreateNewRecord($article,["url" => "...", "section" => "secondary_images"]);
	 *	$image = Image::CreateNewRecord($article,["url" => "..."], "secondary_images"); // section can be defined as the 3rd parameter
	 */
	static function CreateNewFor($obj, $values = array(),$options = array()) {
		if(is_string($options)){
			$values["section"] = $options;
			$options = array();
		}
		$values += array(
			"section" => "",
		);
		$values["table_name"] = $obj->getTableName();
		$values["record_id"] = $obj->getId();
		return static::CreateNewRecord($values,$options);
	}

	/**
	 * Vrati objekty pripojene k jinemu objektu
	 *
	 *	$images = Image::GetInstancesFor($article);
	 *	$images = Image::GetInstancesFor($article,"secondary");
	 *
	 * @param $obj LinkedObject
	 * @param $options @see TableRecord::Find
	 * @return Comment[]
	 *
	 */
	static function GetInstancesFor($obj, $options = array()){
		if(!is_array($options)){ // string or null
			$options = array(
				"section" => (string)$options
			);
		}
		$options += array(
			"section" => "",
			"use_cache" => true,
			"limit" => null,
			"offset" => 0,
		);

		if(is_null($obj)){
			return null;
		}

		$c_class = get_called_class(); // "Image", "Attachment"...
		if(!isset(self::$CACHE[$c_class])){
			static::$CACHE[$c_class] = array();
		}

		$object_class = get_class($obj);

		$table_name = $obj->getTableName(); // "pages", "articles"...
		if(!isset(self::$CACHE[$c_class][$table_name])){
			static::$CACHE[$c_class][$table_name] = array();
		}

		$record_id = $obj->getId();

		$section = (string)$options["section"];

		if(!isset(static::$CACHE[$c_class][$table_name][$record_id])){

			$MAX_IDS_TO_READ = 100;

			$ids_to_read = array($record_id);
			static::$CACHE[$c_class][$table_name][$record_id] = array();

			foreach(Cache::CachedIds($object_class) as $_record_id){
				if(!isset(static::$CACHE[$c_class][$table_name][$_record_id])){
					static::$CACHE[$c_class][$table_name][$_record_id] = array();
					$ids_to_read[] = $_record_id;
				}
				if(sizeof($ids_to_read)>=$MAX_IDS_TO_READ){
					break;
				}
			}

			$o = new $c_class();
			$c_class_table_name = $o->getTableName();
			$dbmole = static::GetDbmole();
			$rows = $dbmole->selectRows("SELECT record_id,section,id FROM $c_class_table_name WHERE table_name=:table_name AND record_id IN :record_ids ORDER BY rank,id",array(
				":table_name" => $table_name,
				":record_ids" => $ids_to_read,
			));
			foreach($rows as $row){
				$_record_id = (int)$row["record_id"];
				static::$CACHE[$c_class][$table_name][$_record_id][] = ["id" => (int)$row["id"], "section" => $row["section"]];
				Cache::Prepare($c_class,(int)$row["id"]);
			}
		}

		$ids = static::$CACHE[$c_class][$table_name][$record_id];
		$ids = array_filter($ids,function($item) use($section){ return $item["section"]===$section; });
		$ids = array_map(function($item){ return $item["id"]; },$ids);
		$ids = array_values($ids);

		$records = Cache::Get($c_class,$ids);
		if($options["offset"]>0 || isset($options["limit"])){
			$records = array_slice($records,$options["offset"],$options["limit"]);
		}
		return $records;
	}

	static function CopyFromTo($from, $to, $options = array()){
		$options += array('exclude' => false);

		foreach(static::GetInstancesFor($from) as $object) {
			$asArray = $object->toArray();
			unset($asArray['id']);

			$values  = array(
					'record_id' => $to->getId(),
					'created_at' => date('c'),
					'updated_at' => date('c'),
					);

			if($from->hasKey("created_by_user_id") && $to->hasKey("created_by_user_id")) {
				$values += array(
					'created_by_user_id' => $to->getCreatedByUserId(),
				);
			}
			if($from->hasKey("updated_by_user_id") && $to->hasKey("updated_by_user_id")) {
				$values += array(
					'updated_by_user_id' => $to->getUpdatedByUserId(),
				);
			}

			static::CreateNewRecord($values + $asArray);
		}

	}

	function setRank($new_rank){
		static::_ClearCache($this->g("table_name"),$this->g("record_id"));
		return $this->_setRank($new_rank,array(
			"table_name" => $this->g("table_name"),
			"record_id" => $this->g("record_id"),
			"section" => $this->g("section"),
		));
	}

	static protected function _ClearCache($table_name,$record_id){
		$record_id = self::ObjToId($record_id);
		$class = get_called_class();
		if(!isset(static::$CACHE[$class])){ static::$CACHE[$class] = array(); }
		if(!isset(static::$CACHE[$class][$table_name])){ static::$CACHE[$class][$table_name] = array(); }
		unset(static::$CACHE[$class][$table_name][$record_id]);
	}
}

