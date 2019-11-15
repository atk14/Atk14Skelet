<?php
/**
 * Z {link_to controller="pages" action="detail" id="4"}
 * udela
 * /zvitezili-jsme-v-soutezi-prodejna-roku-2012/
 */
class PagesRouter extends Atk14Router{

	function recognize($uri){
		if($this->namespace=="" && preg_match('/^\/([a-z0-9-_\/]+?)\/?$/',$uri,$matches) && ($page = Page::GetInstanceByPath($matches[1],$lang))){
			$this->action = "detail";
			$this->controller = "pages";
			$this->params["id"] = $page->getId();
			$this->lang = $lang;
		}
	}

	function build(){
		if($this->namespace!="" || $this->controller!="pages" || $this->action!="detail"){ return; }

		if($page = Cache::Get("Page",$this->params->getInt("id"))){
    	$this->params->del("id");
			
			if($page->getCode()=="homepage"){
				return Atk14Url::BuildLink(array(
					"controller" => "main",
					"action" => "index",
					"lang" => $this->lang,
				));
			}

    	return sprintf('/%s/',$page->getPath($this->lang));
		}
	}
}
