<?php
trait TraitUrlParams {

	static function CreateNewRecord($values,$options = []){
		if(!array_key_exists("url_params",$values) && array_key_exists("url",$values)){
			$values["url_params"] = self::_GetUrlParamsJson($values["url"]);
			
		}
		return parent::CreateNewRecord($values,$options);
	}

	function setValues($values,$options = []){
		if(array_key_exists("url",$values) && !array_key_exists("url_params",$values)){
			$values["url_params"] = self::_GetUrlParamsJson($values["url"]);
		}

		return parent::setValues($values,$options);
	}

	/**
	 *
	 *	$obj->getUrl();
	 *	$obj->getUrl("cs");
	 *	$obj->getUrl(["with_hostname" => true]);
	 */
	function getUrl($lang = null,$options = []){
		global $ATK14_GLOBAL;

		if(is_array($lang)){
			$options = $lang;
		}else{
			$options += [
				"lang" => $lang,
			];
		}

		$options += [
			"with_hostname" => false,
			"lang" => null,
		];

		$lang = $options["lang"];
		unset($options["lang"]);

		$params = $this->getUrlParams();
		if($params){ $params = json_decode($params,true); }
		if(is_null($params)){
			return $this->g("url");
		}
		
		if(is_null($lang)){
			$lang = $ATK14_GLOBAL->getLang();
		}

		$anchor = null;
		if(preg_match('/#(.+)$/',$this->g("url"),$matches)){
			$anchor = $matches[1];
		}
		$options += [
			"anchor" => $anchor
		];

		$params["lang"] = $lang;
		return Atk14Url::BuildLink($params,$options);
	}

	static protected function _GetUrlParamsJson($uri){
		if(!preg_match('/^\/([^\/].*|)$/',$uri)){
			return null;
		}

		$uri = preg_replace('/#.*$/','',$uri); // removing anchor

		$params = Atk14Url::RecognizeRoute($uri,["get_params" => Atk14Url::ParseParamsFromUri($uri)]);

		if($params["action"] == "error404"){ return null; }

		$out = $params["get_params"];
		foreach(["namespace","controller","action","lang"] as $k){
			$out[$k] = $params[$k];
		}

		return json_encode($out);
	}
}
