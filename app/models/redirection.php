<?php
class Redirection extends ApplicationModel {

	function GetInstanceByHttpRequest($request){
		$url = $request->getUrl(); // "http://example.com/documents/manual.pdf"
		$url_without_proto = preg_replace('/^https?:/','',$url); // "//example.com/documents/manual.pdf"
		$uri = $request->getUri(); // "/documents/manual.pdf";

		$dbmole = self::GetDbmole();
		$rows = $dbmole->selectIntoAssociativeArray("SELECT source_url AS key, id, source_url, target_url, moved_permanently FROM redirections WHERE NOT regex ORDER BY LENGTH(source_url) DESC");

		$id = null;
		if(isset($rows[$url])){
			$id = $rows[$url]["id"];
		}elseif(isset($rows[$url_without_proto])){
			$id = $rows[$url_without_proto]["id"];
		}elseif(isset($rows[$uri])){
			$id = $rows[$uri]["id"];
		}

		if(!is_null($id)){ return Cache::Get("Redirection",$id); }

		foreach($rows as $source_url => $row){
			if(strpos($url,$source_url)===0){
				$id = $row["id"];
				break;
			}
			if(strpos($url_without_proto,$source_url)===0){
				$id = $row["id"];
				break;
			}
			if(strpos($uri,$source_url)===0){
				$id = $row["id"];
				break;
			}
		}

		if(!is_null($id)){ return Cache::Get("Redirection",$id); }
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
		$updated = $this->dbmole->selectSingleValue("UPDATE redirections SET last_accessed_at=:last_access WHERE last_accessed_at IS NULL OR last_accessed_at<:last_access RETURNING id",array(":last_access" => $last_access));
		if(!is_null($updated)){
			$this->_readValues();
			return true;
		}
		return false;
	}
}
