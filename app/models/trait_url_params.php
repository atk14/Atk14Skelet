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

		$url = $this->g("url");

		$options += [
			"with_hostname" => (bool)preg_match('/^https?:\/\//',(string)$url),
			"lang" => null,
			"ssl" => preg_match('/^https:\/\//',(string)$url) ? true : null,
		];

		$lang = $options["lang"];
		unset($options["lang"]);

		$params = $this->getUrlParams();
		if($params){ $params = json_decode($params,true); }
		if(is_null($params)){
			return $url;
		}
		
		if(is_null($lang)){
			$lang = $ATK14_GLOBAL->getLang();
		}

		$anchor = null;
		if(preg_match('/#(.+)$/',(string)$url,$matches)){
			$anchor = $matches[1];
		}
		$options += [
			"anchor" => $anchor
		];

		$params["lang"] = $lang;
		return Atk14Url::BuildLink($params,$options);
	}

	static protected function _GetUrlParamsJson($uri){
		global $HTTP_REQUEST;
		$uri = (string)$uri;

		if(!preg_match('/^\/([^\/].*|)$/',(string)$uri) && !preg_match('/^https?:\/\/'.preg_quote($HTTP_REQUEST->getHttpHost(),'/').'\//',$uri) && !preg_match('/^https?:\/\/'.preg_quote(ATK14_HTTP_HOST,'/').'\//',$uri)){
			return null;
		}

		$uri = preg_replace('/^https?:\/\/.+?\//','/',$uri);

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
