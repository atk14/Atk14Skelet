<?php
/**
 * The base class of all the application db table based models.
 * Do you have any common methods or attributes for all your models? Put them right here.
 */
class ApplicationModel extends TableRecord{

	function __construct($table_name = null,$options = array()){
		parent::__construct($table_name,$options);
	}

	static function CreateNewRecord($values,$options = array()){
		$class_name = get_called_class();
		$obj = new $class_name();

		// there is a auto setting of created_at, created_on or create_date field
		$v_keys = array_keys($values);
		foreach(array("created_at","created_on","create_date") as $f){
			if($obj->hasKey($f) && !in_array($f,$v_keys)){
				$values[$f] = date("Y-m-d H:i:s");
			}
		}

		return parent::CreateNewRecord($values,$options);
	}
	
	/**
	 * Converts object into XML.
	 *
	 * @return string
	 */
	function toXml(){
		$class_name = new String4(get_class($this));
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

	/**
	 * Returns a hard to guess unique identifier for a given object.
	 *
	 * <code>
	 * $album = Album::FindById(123);
	 * $token = $album->getToken();
	 * $token2 = $album->getToken("s.e.c.r.e.t");
	 *
	 * Album::GetInstanceByToken($token); // object
	 * Album::GetInstanceByToken($token2,"s.e.c.r.e.t"); // object
	 *
	 * Album::GetInstanceByToken($token,"s.e.c.r.e.t"); // null
	 * Album::GetInstanceByToken($token2); // null
	 * </code>
	 */
	function getToken($extra_salt = ""){
		$length = 32;
		return $this->getId().".".substr(md5(get_class($this).$this->getId().SECRET_TOKEN.$extra_salt),0,$length);
	}

	/**
	 * Instantiates an object according to a given token.
	 *
	 * Returns null when token is not valid.
	 *
	 * @see getToken
	 */
	static function GetInstanceByToken($token,$extra_salt = ""){
		$class_name = get_called_class();
		$ar = explode(".",$token);

		if(isset($ar[0]) && is_numeric($ar[0]) && ($obj = call_user_func(array($class_name,"GetInstanceById"),$ar[0])) && $obj->getToken($extra_salt)==$token){
			return $obj;
		}
	}
}
