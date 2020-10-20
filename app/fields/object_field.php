<?php
class ObjectField extends CharField{
	function __construct($options = array()){
		$options += array(
			"class_name" => null,
			"null_empty_output" => true,
			"return_object" => true, // Return an object or its id?
			"suggesting" => true,
			"widget" => new TextInput(),
		);

		if(!$options["class_name"]){
			$options["class_name"] = String4::ToObject(get_class($this))->gsub('/Field$/','')->toString(); // PersonField -> Person
		}

		$this->class_name = $options["class_name"];
		unset($options["class_name"]);

		$this->return_object = $options["return_object"];
		unset($options["return_object"]);

		if($options["suggesting"]){
			$action = String4::ToObject(get_class($this))->gsub('/Field$/','')->pluralize()->underscore()->toString(); // PersonField -> people

			$options["widget"]->attrs["data-suggesting"] = "yes";
			$options["widget"]->attrs["data-suggesting_url"] = Atk14Url::BuildLink(array("namespace" => "api", "controller" => "suggestions", "action" => $action, "format" => "json"));
		}
		unset($options["suggesting"]);

		parent::__construct($options);

		$this->update_messages(array(
			"not_found" => _("There is no such record"),
		));
	}

	function format_initial_data($value){
		return self::FormatInitialData($value,$this->class_name);
	}

	static function FormatInitialData($value,$class_name){
		if(!$value){ return ""; }

		if(is_numeric($value)){
			$value = $class_name::GetInstanceById($value);
		}

		if(is_object($value)){
			return $value->toHumanReadableString()." [#".$value->getId()."]";
		}

		return $value; // hmm.. neni jasne, co to je
	}

	function clean($value){
		$value = trim($value);
		$value = preg_replace('/^.*?(\d+)$/','\1',$value); // "John Brock #1234" -> "1234"
		$value = preg_replace('/^.*?\[#(\d+)\]$/','\1',$value); // "John Brock [#1234]" -> "1234"
		
		list($err,$value) = parent::clean($value);

		if($err || is_null($value)){ return array($err,$value); }
		
		$class_name = $this->class_name;
		$object = $class_name::GetInstanceById($value);
		
		if(!$object){
			return array($this->messages["not_found"],null);
		}

		return array(null,$this->return_object ? $object : $object->getId());
	}
}
