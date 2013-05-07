<?php
/**
 * == in controller ==
 *
 * $navi = new Navigation();
 * $navi->add(
 *  "Profile", // link text
 *  array("controller" => "users", "action" => "detail"), // URL
 *  array("active" => "$this->controller/$this->action"=="users/detail") // options
 * );
 *
 * == in template ==
 *
 *  <ul class="nav">
 *    {!$navi}
 *  </ul>
 *
 *  {* or *}
 *
 *  <ul class="nav">
 *  {foreach from=$navi->getItems() item=item}
 *    {!$item}
 *  {/foreach}
 *  </ul>
 */
class Navigation{
	var $items = array();

	function __construct(){
		//...?
	}

	/**
	 * $context_menu->addItem("Update Account Data","users/edit",array("active" => true));
	 */
	function addItem($item,$url = null,$options = array()){
		$item = $this->_makeItem($item,$url,$options);
		if($item->key){
			$this->items[$item->key] = $item;
		}else{
			$this->items[] = $item;
		}	
	}
	/**
	 * Alias for Navigation::addItem();
	 */
	function add($item,$url = null,$options = array()){ return $this->addItem($item,$url,$options); }

	/**
	 * $navigation->addHeader("User functions");
	 */
	function addHeader($title){
		$this->addItem(array(
			"text" => $title,
			"class" => "nav-header",
		));
	}

	function addDivider(){
		$this->addItem(array(
			"class" => "divider",
		));
	}

	function isEmpty(){ return sizeof($this->items)==0; }
	function hasItems(){ return sizeof($this->items)>0; }
	function getItems(){ return $this->items; }

	function _makeItem($item,$url = null,$options = array()){
		if(is_string($url)){
			if(preg_match('/^[a-z]/',$url)){
				$url = Atk14Url::BuildLink(array(
					"action" => $url
				));
			}
		}
		if(is_array($url)){
			$url = Atk14Url::BuildLink($url);
		}
		if(is_string($item) && is_string($url)){
			$options += array(
				"text" => $item,
				"url" => $url
			);
			return $this->_makeItem($options);
		}
		if(is_array($item)){ $item = new NavigationItem($item); }
		return $item;
	}

	function __toString(){
		return join("\n",$this->items);
	}
}

class NavigationItem{
	function __construct($options = array()){
		$options += array(
			"key" => null,
			"text" => "",
			"title" => "",
			"url" => "",
			"active" => false,
			"class" => "", // css class name of outer element (<li>)
		);
		$this->key = $options["key"];
		$this->title = $options["title"];
		$this->url = $options["url"];
		$this->active = $options["active"];
		$this->text = $options["text"];
		$this->class = $options["class"];
	}

	function getMarkup(){
		$out = $this->text;
		$title = $this->title ? " title=\"".htmlspecialchars($this->title)."\"" : "";
		$class = trim($this->class . ($this->active ? " active" : ""));
		$class = $class ? " class=\"".htmlspecialchars($class)."\"" : "";

		if($this->url){
			$out = "<a href=\"$this->url\">$out</a>";
		}

		$out = "<li$title$class>$out</li>";

		return $out;
	}

	function __toString(){
		return $this->getMarkup();
	}
}
