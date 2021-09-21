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

		$url = $request->getUrl(); // "http://example.com/documents/manual.pdf"
		$url_without_proto = preg_replace('/^https?:/','',$url); // "//example.com/documents/manual.pdf"
		$uri = $request->getUri(); // "/documents/manual.pdf";

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
			if($strict_match){
				return $url === $source_url;
			}
			return strpos($url,$source_url)===0;
		};

		foreach($rows as $source_url => $row){
			foreach([$url,$url_without_proto,$uri] as $u){
				if($match($u,$source_url)){
					$id = $row["id"];
					break 2;
				}
				$u_without_params = preg_replace('/\?.*$/','',$u);
				if($u!==$u_without_params && $match($u_without_params,$source_url)){
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

	static function _ReadRows($recache = false){
		$dbmole = static::GetDbmole();
		$rows = $dbmole->selectIntoAssociativeArray("SELECT source_url AS key, id, source_url, target_url FROM error_redirections WHERE NOT regex ORDER BY LENGTH(source_url) DESC",array(),array("cache" => true, "recache" => $recache));
		$regex_rows = $dbmole->selectIntoAssociativeArray("SELECT source_url AS key, id, source_url, target_url FROM error_redirections WHERE regex ORDER BY LENGTH(source_url) DESC",array(),array("cache" => true, "recache" => $recache));

		return array($rows,$regex_rows);
	}
}
