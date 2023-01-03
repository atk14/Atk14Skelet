<?php
trait TraitGetInstanceByCode {
	
	static function GetInstanceByCode($code,$options = array()){
		static $instances;

		$options += array(
			"refresh_cache" => false,
		);

		if($options["refresh_cache"]){
			$instances = null;
			Cache::Clear(get_called_class());
		}

		if(!isset($instances)){
			$instances = [];
			foreach(self::FindAll("code IS NOT NULL",[],["use_cache" => true]) as $c){
				$instances[$c->getCode()] = $c;
			}
		}

		$code = (string)$code;
		return isset($instances[$code]) ? $instances[$code] : null;
	}
}
