<?php
class SuggestionsController extends ApiController{

	function users(){
		if(!$this->logged_user || !$this->logged_user->isAdmin()){
			// this is for a logged-in administrator only
			$this->_execute_action("error403");
			return;
		}

		$this->_suggest([
			"fields" => ["login","firstname","lastname"],
			"order_by" => "LOWER(login) LIKE LOWER(:q)||'%' DESC, LOWER(lastname) LIKE LOWER(:q)||'%' DESC, LOWER(firstname) LIKE LOWER(:q)||'%' DESC, login",
		]);
	}

	function _suggest($options = array()){
		global $ATK14_GLOBAL;

		$options += array(
			"class_name" => "", // "Person"
			"field_class_name" => "", // PersonField
			"fields" => array("name"),
			"order_by" => "LOWER(name),name,id",
			"conditions" => array(), // array("deleted='f'")
			"bind_ar" => array(),
		);

		if(!$options["class_name"]){
			$options["class_name"] = String4::ToObject($this->action)->singularize()->camelize()->toString(); // "people" -> "Person"
		}

		if(!$options["field_class_name"]){
			$options["field_class_name"] = String4::ToObject($this->action)->singularize()->camelize()->toString()."Field"; // "people" -> "PersonField"
		}

		$class_name = (string)$options["class_name"];
		$field_class_name = (string)$options["field_class_name"];

		$o = new $class_name();
		$table = $o->getTableName();
		$all_translatable_fields = ($o instanceof Translatable) ? $class_name::GetTranslatableFields() : array();

		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			$q = $d["q"];
			$q = preg_replace('/(\[|\[#.*)$/','',$q); // "Jan Brus, šéfredaktor [#123]" -> "Jan Brus, šéfredaktor"

			$this->api_data = array();
			$conditions = $options["conditions"];
			$bind_ar = $options["bind_ar"];
			$bind_ar[":q"] = $q; 

			$q = Translate::Lower($q); // "jan brus, šéfredaktor"

			$_fields = array();
			foreach($options["fields"] as $f){
				if(!in_array($f,$all_translatable_fields)){ $_fields[] = $f; continue; }
				foreach($ATK14_GLOBAL->getSupportedLangs() as $l){
					$_fields[] = "(SELECT body FROM translations WHERE record_id=$table.id AND table_name='$table' AND key='$f' AND lang='$l')";
				}
			}

			$fields = "LOWER(COALESCE(''||".join(",'')||' '||COALESCE(''||",$_fields).",''))";
			if(!$condition = FullTextSearchQueryLike::GetQuery($fields,$q,$bind_ar)){
				return;
			}

			$conditions[] = $condition;
			
			$records = $class_name::FindAll(array(
				"conditions" => $conditions,
				"bind_ar" => $bind_ar,
				"order_by" => $options["order_by"],
				"limit" => 20,
			));

			$f = new $field_class_name();
			foreach($records as $p){
				$this->api_data[] = $f->format_initial_data($p);
			}
		}
	}

	function _before_filter(){
		parent::_before_filter();

		// vsechny akce maji stejny formular
		$this->form = $this->_get_form("index");
	}
}
