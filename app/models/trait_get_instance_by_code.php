<?php
trait TraitGetInstanceByCode {
	
	static function GetInstanceByCode($code,$options = array()){
		static $rows;

		$options += array(
			"refresh_cache" => false,
		);

		if($options["refresh_cache"]){
			$rows = null;
			Cache::Clear(get_called_class());
		}

		if(!isset($rows)){
			$obj = new self();
			$dbmole = $obj->getDbMole();
			$table_name = $obj->getTableName();
			$id = $obj->getIdFieldName();
			$rows = $dbmole->selectIntoAssociativeArray("SELECT code AS key, $id FROM $table_name WHERE code IS NOT NULL");

			// prevent to cache a lot of records
			if(sizeof($rows)<=20){
				Cache::Prepare(get_called_class(),$rows);
			}
		}

		$code = (string)$code;
		return isset($rows[$code]) ? Cache::Get(get_called_class(),$rows[$code]) : null;
	}
}
