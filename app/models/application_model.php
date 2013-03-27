<?php
/**
 * The base class of all the application db table based models.
 * Do you have any common methods or attributes for all your models? Put them right here.
 * Otherwise there's no need to care :)
 */
class ApplicationModel extends TableRecord{

	function __construct($table_name = null,$options = array()){
		parent::__construct($table_name,$options);
	}
	
	/**
   * Converts object into XML.
	 * 
	 * @return string
	 */
	function toXml(){
		$class_name = new String(get_class($this));
		$root = $class_name->underscore(); // "LittleKitty" turns into "little_kitty"
		$out = array();
		$out[] = "<$root>";
		foreach($this->toExportArray() as $k => $v){
			$out[] = "<$k>".XMole::ToXml($v)."</$k>"; // escaping $v to be placed inside XML
		}
		$out[] = "</$root>";
		return join("\n",$out);
	}

	/**
	 * Converts object into JSON.
	 * 
	 * @return string
	 */
	function toJson(){
		return json_encode($this->toExportArray());
	}

	/**
	 * Returns associative array with object`s attributes and their values.
	 * This array is used for exporting object as XML or JSON.
	 * 
	 * Cover it in a given class if you want to return something else than just $object->toArray().
	 * 
	 * @return array
	 */
	function toExportArray(){ return $this->toArray(); }

	/**
	 *
	 * Provides transparent updating of update_at field if such field exists.
	 */
	function setValues($values,$options = array()){
		$v_keys = array_keys($values);
		foreach(array("updated_at","updated_on","update_date") as $f){
			if($this->hasKey($f) && !in_array($f,$v_keys)){
				$values[$f] = date("Y-m-d H:i:s");
			}
		}
		return parent::setValues($values,$options);
	}
}
