<?php
/**
 * HTTP error 404 redirection
 *
 */
class ErrorRedirection extends ApplicationModel {

	static function GetInstanceByHttpRequest($request,$options = []){
		$options += [
			"strict_match" => false,
		];

		$source_urls = self::_GetPossibleSourceUrls($request->getUrl(),$options);

		return ErrorRedirection::FindFirst([
			"conditions" => [
				"source_url IN :source_urls",
				"regex=FALSE",
			],
			"bind_ar" => [
				":source_urls" => $source_urls,
			],
			"order_by" =>  "LENGTH(source_url) DESC",
		]);

		// stary zpusob je hodne pomaly
		list($rows,$regex_rows) = static::_ReadRows();

		$id = null;
		if(isset($rows[$url])){
			$id = $rows[$url]["id"];
		}elseif(isset($rows[$url_without_proto])){
			$id = $rows[$url_without_proto]["id"];
		}elseif(isset($rows[$uri])){
			$id = $rows[$uri]["id"];
		}

		if(!is_null($id)){ return Cache::Get("ErrorRedirection",$id); }

		$strict_match = $options["strict_match"];
		$match = function($url,$source_url) use($strict_match){
			if(strlen($source_url)>strlen($url)){ return false; }
			if($strict_match){
				return $url === $source_url;
			}
			return strpos($url,$source_url)===0;
		};

		foreach($rows as $source_url => $row){
			$source_url = self::_NormalizeUrl($source_url);
			foreach([$url,$url_without_proto,$uri] as $u){
				if($match($u,$source_url)){
					$id = $row["id"];
					break 2;
				}
				if(!strpos($source_url,"?")){ continue; }
				$u_without_params = preg_replace('/\?.*$/','',$u);
				if($match($u_without_params,$source_url)){
					$id = $row["id"];
					break 2;
				}
			}
		}

		if(!is_null($id)){ return Cache::Get("ErrorRedirection",$id); }
	}

	static function RefreshCache(){
		static::_ReadRows(true);
	}

	function getDestinationUrl(){
		if(!$this->g("regex")){
			return $this->getTargetUrl();
		}
	}

  function movedPermanently(){
    return $this->g("moved_permanently");
  }

  function regex(){
    return $this->g("regex");
  }

	/**
	 * Touches the date of the last access
	 */
	function touch($time = null){
		if(is_null($time)){ $time = time(); }
		if(!is_numeric($time)){ $time = strtotime($time); }

		$last_access = date("Y-m-d H:i:s",$time);

		if(!is_null($this->getLastAccessedAt()) && strtotime($this->getLastAccessedAt())>strtotime($last_access)){
			return false;
		}

		// Avoiding a parallel update
		$updated = $this->dbmole->selectSingleValue("UPDATE error_redirections SET last_accessed_at=:last_access WHERE id=:id AND (last_accessed_at IS NULL OR last_accessed_at<:last_access) RETURNING id",array(":id" => $this, ":last_access" => $last_access));
		if(!is_null($updated)){
			$this->_readValues();
			return true;
		}
		return false;
	}

	static protected $_Rows = null;
	static protected $_RegexRows = null;
	static function _ReadRows($recache = false){
		if($recache){
			self::$_Rows = null;
			self::$_RegexRows = null;
		}
		if(is_null(self::$_Rows)){
			$dbmole = static::GetDbmole();
			self::$_Rows = $dbmole->selectIntoAssociativeArray("SELECT source_url AS key, id, source_url, target_url FROM error_redirections WHERE NOT regex ORDER BY LENGTH(source_url) DESC",array(),array("cache" => true, "recache" => $recache));
			self::$_RegexRows = $dbmole->selectIntoAssociativeArray("SELECT source_url AS key, id, source_url, target_url FROM error_redirections WHERE regex ORDER BY LENGTH(source_url) DESC",array(),array("cache" => true, "recache" => $recache));
		}

		return array(self::$_Rows,self::$_RegexRows);
	}

	static function _NormalizeUrl($url){
		if(!strpos($url,"?")){ return $url; }
		if(!preg_match('/^(.*?)\?(.+)$/',$url,$matches)){ return $url; }

		$url_without_params = $matches[1];
		$params = $matches[2];

		$params_ar = [];
		foreach(explode("&",$params) as $item){
			if($item==""){ continue; }
			$item_ar = explode("=",$item);
			if(sizeof($item_ar)==1){
				$v = $item_ar[0];
				$v = urldecode($v);
				$v = urlencode($v);
				$params_ar[] = "$v";
				continue;
			}
			list($k,$v) = $item_ar;
			$k = urldecode($k);
			$v = urldecode($v);
			$k = urlencode($k);
			$v = urlencode($v);
			$params_ar[] = "$k=$v";
		}
		$params = join("&",$params_ar);

		return "$url_without_params?$params";
	}

	static function _GetPossibleSourceUrls($url,$options = []){
		$options += [
			"strict_match" => false,
		];

		$url_without_proto = preg_replace('/^https?:/','',$url); // "//example.com/documents/manual.pdf"
		$uri = preg_replace('/^\/\/[^\/]+/','',$url_without_proto);
		$out[] = $url;
		$out[] = $url_without_proto;
		$out[] = $uri;

		if($options["strict_match"]){ return $out; }

		$last_parameter_pattern = '/[&?][^&?]*$/';
		while(1){
			$_url = preg_replace($last_parameter_pattern,'',$url);
			if($_url===$url){
				break;
			}
			$url = $_url;
			$url_without_proto = preg_replace($last_parameter_pattern,'',$url_without_proto);
			$uri = preg_replace($last_parameter_pattern,'',$uri);
			$out[] = $url;
			$out[] = $url_without_proto;
			$out[] = $uri;
		}
		return $out;
	}
}
