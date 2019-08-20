<?php
trait TraitGetInstanceByCode {
	
	static function GetInstanceByCode($code){
		static $instances;

		if(!$instances){
			$instances = [];
			foreach(self::FindAll("code IS NOT NULL",[],["use_cache" => true]) as $c){
				$instances[$c->getCode()] = $c;
			}
		}

		$code = (string)$code;
		return isset($instances[$code]) ? $instances[$code] : null;
	}
}
