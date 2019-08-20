<?php
class IobjectsBaseController extends AdminController{

	function _before_filter(){
		// potrebujeme tyto 2 parametry
		if(
			in_array($this->action,array("create_new")) && (
				!($this->table_name = $this->tpl_data["table_name"] = (string)$this->params["table_name"]) ||
				!($this->record_id = $this->tpl_data["record_id"] = (int)$this->params["record_id"])
			)
		){
			return $this->_execute_action("error404");
		}

		$iobject = null;
		if(in_array($this->action,array("detail","edit"))){
			$iobject = $this->_find($this->_get_iobject_type());
		}
	}

	function _get_iobject_type(){
		return String4::ToObject($this->controller)->singularize()->toString();
	}

	function _get_iobject_class_name(){
		return String4::ToObject($this->controller)->singularize()->camelize()->toString(); // "pictures" -> "Picture"
	}

	function _get_iobject_type_humanized($iobject){
		Atk14Require::Helper("modifier.iobject_type");
		$snippet = smarty_modifier_iobject_type($iobject);
		if(preg_match('/title="(.*?)"/',$snippet,$matches)){
			return $matches[1];
		}
	}

	function detail(){
		$this->_set_up_breadcrumbs();
		$iobject_type = $this->_get_iobject_type(); // "picture"
		$object = $this->$iobject_type;
		$title = $this->_get_iobject_type_humanized($object);// "Obrázek"
		if($object->getTitle()){ $title .= ": ".strip_tags($object->getTitle()); }
		$this->page_title = $title;
	}

	function create_new(){
		$iobject_class_name = $this->_get_iobject_class_name();
		$this->_create_new(array(
			"create_closure" => function($d) use($iobject_class_name){
				$iobject = $iobject_class_name::CreateNewRecord($d);
				IobjectLink::CreateNewRecord(array(
					"iobject_id" => $iobject,
					"linked_table" => $this->table_name,
					"linked_record_id" => $this->record_id,
				));
				return $iobject;
			}
		));
	}

	function edit(){
		$this->_set_up_breadcrumbs();
		$iobject_type = $this->_get_iobject_type(); // "picture"
		$object = $this->$iobject_type;

		$this->_edit();
	}

	/**
	 * Prida do breadcrumbs odkaz na editaci clanku, starnky...
	 */
	function _set_up_breadcrumbs(){
		$iobject_type = $this->_get_iobject_type(); // "picture"
		if(!isset($this->$iobject_type)){ return; }
		$object = $this->$iobject_type;

		$link = IobjectLink::FindFirst("iobject_id",$object);

		if(!$link || !($object = $link->getLinkedObject())){
			return;
		}

		if(method_exists($object,"getTitle")){
			$title = $object->getTitle();
		}elseif($object->hasKey("title")){
			$title = $object->g("title");
		}else{
			$title = "$object";
		}
		$title = strip_tags($title);

		$ctrl = $link->getLinkedTable(); // "articles"
		$this->breadcrumbs[] = array($title,$this->_link_to(array("action" => "$ctrl/edit", "id" => $object)));
	}
}
