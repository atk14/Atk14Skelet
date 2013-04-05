<?
/**
* $cm = new ContextMenu();
* $cm->setTitle("Aktivity s domenou plovarna.cz");
* $cm->addItem(array(
*		"text" => "Nechat si poslat Auth-Info",
*		"url" => $this->_link_to(...),
*	));
* $cm->addFaq(81);
*
*	
*/
class ContextMenu{
	function ContextMenu($title = ""){
		$this->title = $title;
		$this->extended_title = "";
		$this->items = array();
		$this->extended_items = array();
	}

	function setTitle($title){ $this->title = $title; }

	function _makeItem($item,$url = null,$options = array()){
		if(is_string($url)){
			if(preg_match('/^[a-z]/',$url)){
				$url = Atk14Url::BuildLink(array(
					"action" => $url
				));
			}
		}
		if(is_string($item) && is_string($url)){
			$options += array(
				"text" => $item,
				"url" => $url
			);
			return $this->_makeItem($options);
		}
		if(is_array($item)){ $item = new ContextMenuItem($item); }
		return $item;
	}

	/**
	 * Alias for ContextMenu::addItem();
	 */
	function add($item,$url = null,$options = array()){ return $this->addItem($item,$url,$options); }

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

	function isEmpty(){ return sizeof($this->items) + sizeof($this->extended_items)==0; }
	function hasTitle(){ return strlen($this->title)>0; }

	function getItems(){ return $this->items; }
	function getTitle(){ return $this->title; }
	function hasItems(){ return sizeof($this->items)>0; }
}

class ContextMenuItem{
	function ContextMenuItem($options = array()){
		$options = array_merge(array(
			"key" => null,
			"text" => "",
			"title" => "",
			"url" => "",
			"active" => false,
			"class" => "", // nazev css stylu
			"attrs" => array(),
		),$options);
		$this->key = $options["key"];
		$this->title = $options["title"];
		$this->url = $options["url"];
		$this->active = $options["active"];
		$this->text = $options["text"];
		$this->class = $options["class"];
		$this->attrs = $options["attrs"];
	}

	function getMarkup(){
		$out = $this->text;
		if($this->url){
			$title = $this->title ? " title=\"".htmlspecialchars($this->title)."\"" : "";
			$class = $this->class ? " class=\"".htmlspecialchars($this->class)."\"" : "";
			$attrs = "";
			foreach($this->attrs as $key => $value){
				$attrs .= " $key=\"".htmlspecialchars($value)."\"";
			}
			$out = "<a href=\"$this->url\"$title$class$attrs>$out</a>";
		}
		return $out;
	}

	function isLink(){ return strlen($this->url)>0; }
}
