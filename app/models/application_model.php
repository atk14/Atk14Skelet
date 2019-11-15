<?php
require_once(__DIR__ . "/translatable.php");
require_once(__DIR__ . "/rankable.php");
require_once(__DIR__ . "/i_slug.php");

/**
 * The base class of all the application db table based models.
 * Do you have any common methods or attributes for all your models? Put them right here.
 */
class ApplicationModel extends TableRecord{

	function __construct($table_name = null,$options = array()){
		parent::__construct($table_name,$options);
	}

	static function CreateNewRecord($values,$options = array()){
		global $ATK14_GLOBAL,$HTTP_REQUEST;
		$obj = new static();

		// there is a auto setting of created_at, created_on or create_date field
		$v_keys = array_keys($values);
		foreach(array("created_at","created_on","create_date") as $f){
			if($obj->hasKey($f) && !in_array($f,$v_keys)){
				$values[$f] = date("Y-m-d H:i:s");
			}
		}

		if($obj->hasKey("created_from_addr") && !in_array("created_from_addr",$v_keys)){
			$values["created_from_addr"] = $HTTP_REQUEST->getRemoteAddr();
		}

		if($obj->hasKey("created_from_hostname") && !in_array("created_from_hostname",$v_keys)){
			$values["created_from_hostname"] = $HTTP_REQUEST->getRemoteHostname();
		}

		if($obj->hasKey("created_from_user_agent") && !in_array("created_from_user_agent",$v_keys)){
			$values["created_from_user_agent"] = String4::ToObject($HTTP_REQUEST->getUserAgent())->truncate(1000)->toString();
		}

		$tr_strings = array();
		if($obj instanceof Translatable && ($fields = static::GetTranslatableFields())){
			foreach($values as $k => $v){
				if(preg_match('/^(.+)_([a-z]{2})$/',$k,$matches) && in_array($matches[1], $fields)){
					$tr_strings[$k] = $v;
					unset($values[$k]);
					continue;
				}
			}
		}

		$slugs = array();
		if($obj instanceof iSlug){
			foreach($values as $k => $v){
				if(preg_match('/^slug_([a-z]{2})$/',$k,$matches)){
					$slugs[$matches[1]] = $v;
					unset($values[$k]);
					continue;
				}
			}
		}

		if($obj->hasKey("created_by_user_id") && !array_key_exists("created_by_user_id",$values)){
			$values["created_by_user_id"] = ApplicationModel::_GetLoggedUserId();
		}

		$out = parent::CreateNewRecord($values,$options);

		if($tr_strings){
			Translation::SetObjectStrings($out,$tr_strings);
		}

		if($slugs){
			$out->setSlug($slugs);
		}

		if($obj instanceof iSlug){
			Slug::ComplementSlugs($out);
		}

		return $out;
	}

	function toArray(){
		global $ATK14_GLOBAL;
		$defaults = array();

		if($this instanceof Translatable && ($fields = static::GetTranslatableFields())){
			$langs = $ATK14_GLOBAL->getSupportedLangs();
			foreach($fields as $k){
				foreach($langs as $l){
					$defaults["{$k}_$l"] = null;
				}
			}
		}

		if($this instanceof iSlug){
			foreach($ATK14_GLOBAL->getSupportedLangs() as $l){
				$defaults["slug_$l"] = $this->getSlug($l);
			}
		}

		return parent::toArray() + Translation::GetObjectStrings($this) + $defaults;
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
	 *
	 * @param array $values
	 * @param array $options
	 * - set_update_time if true is passed the method does not set fields updated_at, updated_on, update_date [default: true]
	 */
	function setValues($values,$options = array()){
		global $HTTP_REQUEST;

		$options += array(
			"reconstruct_missing_slugs" => false,	
			"set_update_time" => true,
		);
		$reconstruct_missing_slugs = $options["reconstruct_missing_slugs"];
		unset($options["reconstruct_missing_slugs"]);

		$v_keys = array_keys($values);
		if ($options["set_update_time"]) {
			foreach(array("updated_at","updated_on","update_date") as $f){
				if($this->hasKey($f) && !in_array($f,$v_keys)){
					$values[$f] = date("Y-m-d H:i:s");
				}
			}
		}

		if($this->hasKey("updated_by_user_id") && !in_array("updated_by_user_id",$v_keys)){
			$values["updated_by_user_id"] = ApplicationModel::_GetLoggedUserId();
		}

		if($this->hasKey("updated_from_addr") && !in_array("updated_from_addr",$v_keys)){
			$values["updated_from_addr"] = $HTTP_REQUEST->getRemoteAddr();
		}

		if($this->hasKey("updated_from_hostname") && !in_array("updated_from_hostname",$v_keys)){
			$values["updated_from_hostname"] = $HTTP_REQUEST->getRemoteHostname();
		}

		if($this->hasKey("updated_from_user_agent") && !in_array("updated_from_user_agent",$v_keys)){
			$values["updated_from_user_agent"] = String4::ToObject($HTTP_REQUEST->getUserAgent())->truncate(1000)->toString();
		}

		$class_name = get_class($this);

		if(in_array("Translatable", class_implements($this)) && ($translations = $class_name::GetTranslatableFields())){
			$tr_strings = array();
			foreach($values as $k => $v){
				if(preg_match('/^(.+)_([a-z]{2})$/',$k,$matches) && in_array($matches[1],$translations)){
					$tr_strings[$k] = $v;
					unset($values[$k]);
				}
			}
			Translation::SetObjectStrings($this,$tr_strings);
		}

		$slugs = $original_slugs = array();
		if($this instanceof iSlug){
			$slug_segment = $this->getSlugSegment();
			$original_slugs = $this->getSlugs(); // array("cs" => "vitejte", "en" => "welcome")
			$slugs = array();
			foreach($values as $k => $v){
				if(preg_match('/^slug_([a-z]{2})$/',$k,$matches)){
					$slugs[$matches[1]] = $v;
					unset($values[$k]);
				}
			}
			# pozor: pokud byla instance zarazena v jinem segmentu, zustane stary slug a pri vytvareni noveho slugu havaruje
			//$this->setSlug($slugs);
		}

		$out = parent::setValues($values,$options);

		if($this instanceof iSlug){
			if($slug_segment!==$this->getSlugSegment()){
				$slugs += $original_slugs; // je nutne opetovne nastavit i slugy tech jazyku, ktere ve $values nejsou
			}
			if($slugs){
				// slugy se nastavuji az tady, protoze se muzem volani setValues() zmenit segment
				$this->setSlug($slugs,null,null,array("reconstruct_missing_slugs" => $reconstruct_missing_slugs));
			}
		}

		return $out;
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
	function getToken($options = array()){
		if(is_string($options)){
			$options = array("extra_salt" => $options);
		}
		$options += array(
			"salt" => SECRET_TOKEN,
			"extra_salt" => "",
			"hash_length" => 32, // max. 32
		);
		$length = $options["hash_length"];
		return $this->getId().".".substr(md5(get_class($this).$this->getId().$options["salt"].$options["extra_salt"]),0,$length);
	}

	/**
	 * Instantiates an object according to a given token.
	 *
	 * Returns null when token is not valid.
	 *
	 * @see getToken
	 */
	static function GetInstanceByToken($token,$options = array()){
		$token = (string)$token;
		$class_name = get_called_class();
		$ar = explode(".",$token);

		if(isset($ar[0]) && is_numeric($ar[0]) && ($obj = call_user_func(array($class_name,"GetInstanceById"),$ar[0])) && $obj->getToken($options)===$token){
			return $obj;
		}
	}

	/**
	 * Vrati unikatni token dane objektu.
	 *
	 * Unikatnost je zajistena v ramci tridy objektu a segmentu.
	 */
	function getSlug($lang = null,$segment = null){
		if(is_null($segment)){
			$segment = $this->getSlugSegment();
		}
		return Slug::GetObjectSlug($this,$lang,(string)$segment);
	}

	/**
	 * $slugs = $page->getSlugs(); // array("cs" => "vitejte", "en" => "welcome")
	 * $slugs = $page->getSlugs(array("prefix" => "slug_")); // array("slug_cs" => "vitejte", "slug_en" => "welcome")
	 * $slugs = $page->getSlugs(array("segment" => "123"));
	 */
	function getSlugs($options = array()){
		global $ATK14_GLOBAL;

		$options += array(
			"segment" => null,
			"prefix" => "",
		);

		$segment = $options["segment"];
		$prefix = $options["prefix"];

		$slugs = array();
		foreach($ATK14_GLOBAL->getSupportedLangs() as $l){
			$slugs["$prefix$l"] = $this->getSlug($l,$segment);
		}

		return $slugs;
	}

	/**
	 * $obj->setSlug("pocitac-amiga-1200");
	 * $obj->setSlug("pocitac-amiga-1200","cs");
	 * $obj->setSlug(array(
	 *	"cs" => "pocitac-amiga-1200",
	 *	"en" => "computer-amiga-1200"
	 * ));
	 */
	function setSlug($slug,$lang = null,$segment = null,$options = array()){
		if(is_null($segment)){
			$segment = $this->getSlugSegment();
		}
		$segment = (string)$segment;
		if(is_array($slug)){
			foreach($slug as $lang => $s){
				Slug::SetObjectSlug($this,$s,$lang,$segment,$options);
			}
			return;
		}
		Slug::SetObjectSlug($this,$slug,$lang,$segment,$options);
	}

	static function GetInstanceBySlug($slug,&$lang = null,$segment = ''){
		$class_name = get_called_class();
		$o = new $class_name();
		$table_name = $o->getTableName();

		$record_id = Slug::GetRecordIdBySlug($table_name,$slug,$lang,(string)$segment);
		return Cache::Get("$class_name",$record_id);
	}

	function getValue($field_name){
		// getting value of a translatable field
		if($this instanceof Translatable && preg_match('/^(.+)_([a-z]{2})$/',$field_name,$matches)){
			$f = $matches[1]; // e.g. "title"
			$lang = $matches[2]; // e.g. "en"
			
			if(in_array($f,static::GetTranslatableFields())){
				$tr_strings = Translation::GetObjectStrings($this);
				return isset($tr_strings[$field_name]) ? $tr_strings[$field_name] : null;
			}
		}

		return parent::getValue($field_name);
	}

	/**
	 * Tento __call zachytava tato volani:
	 *
	 *	$brand->getInfo();
	 *	$brand->getInfo("en");
	 *
	 * Pokud je info policko z Brand::$translations, tak to zafunguje, podle ocekavani!
	 */
	function __call($name,$arguments){
		global $ATK14_GLOBAL;

		if(! $this instanceof Translatable) {
			return parent::__call($name,$arguments);
		}

		$_name = new String4($name);
		if($_name->match("/^get(.+)/",$matches)){
			$key = $matches[1]->underscore();
			if(in_array($key,static::GetTranslatableFields())){
				$tr_strings = Translation::GetObjectStrings($this);
				$lang = isset($arguments[0]) ? $arguments[0] : $ATK14_GLOBAL->getLang();
				$k = "{$key}_$lang";

				// Fallback handling
				//
				// In config/locale.yml the fallback language could be specified this way:
				//
				//  en:
				//    LANG: en_US.UTF-8
				//
				//  cs:
				//    LANG: cs_CZ.UTF-8
				//    fallback: "en"
				//
				//  sk:
				//    LANG: sk_SK.UTF-8
				//    fallback: "cs"
				//
				if(!isset($tr_strings[$k]) || !strlen($tr_strings[$k])){
					$langs = $ATK14_GLOBAL->getConfig("locale");
					$fallback = isset($langs[$lang]["fallback"]) ? $langs[$lang]["fallback"] : "";
					if($fallback && $fallback!=$lang){
						return self::__call($name,array($fallback));
					}
				}

				return isset($tr_strings[$k]) ? $tr_strings[$k] : null;
			}
		}

		return parent::__call($name,$arguments);
	}

	/**
	 * $book->destroy(); // deleted is set to true
	 * $book->destroy(true); // the book entry is being deleted
	 */
	function destroy($destroy_for_real = null){
		$false_deletion = false;
		if($this->hasKey("deleted") && (!isset($destroy_for_real) || $destroy_for_real==false)){
			if(!$this->g("deleted")){
				$this->s("deleted",true);
			}
			$false_deletion = true;
		}

		Slug::DeleteObjectSlugs($this);

		if($false_deletion){
			return;
		}

		Translation::DeleteObjectStrings($this);
		return parent::destroy();
	}

	/*
	function setRank($new_rank){
		return $this->_setRank($new_rank);
	} */

	/**
	 * // in Brand
	 *	$this->_setRank(0);
	 *
	 * // in Image:
	 * $this->_setRank(2,array("table_name" => $this->g("table_name"),"record_id" => $this->getRecordId()));
	 */
	protected function _setRank($new_rank,$ranked_records_conditions = array()){
		settype($new_rank,"integer");

		$class_name = get_class($this);
		$records = $class_name::FindAll(array(
			"conditions" => $ranked_records_conditions,
			"order_by" => "rank, id"
		));

		$ranks = array();

		$exp_rank = 0;
		foreach($records as $r){
			$id = $r->getId();

			if($id==$this->getId()){
				$ranks[$id] = $new_rank;
				continue;
			}

			if($new_rank==$exp_rank){
				$exp_rank++;
			}

			$ranks[$id] = $exp_rank;
			$exp_rank++;
		}

		//error_log(print_r($ranked_records_conditions,true));
		//error_log(print_r($ranks,true));

		$has_updated_by_user_id = $this->hasKey("updated_by_user_id");
		$has_updated_at = $this->hasKey("updated_at");

		foreach($records as $r){
			if($r->getRank()!=$ranks[$r->getId()]){

				$upd = array("rank" => $ranks[$r->getId()]);

				// pri zmene ranku nenastavujeme updated_by_user_id a updated_at
				if($has_updated_by_user_id){ $upd["updated_by_user_id"] = $r->g("updated_by_user_id"); }
				if($has_updated_at){ $upd["updated_at"] = $r->g("updated_at"); }

				if($r->getId()==$this->getId()){
					$this->s($upd);
				}else{
					$r->s($upd);
				}
			}
		}
	}

	/**
	 * Pomerne drsna metoda pro zjistovani prihlaseneho uzivatele.
	 * Pouziva se pro ukladani hodnot do poli created_by_user_id a updated_by_user_id.
	 *
	 * !! Nebude fungovat, pokud se zmeni zpusob ukladani id prihlaseneho uzivatele v kontrolerech.
	 */
	static protected function _GetLoggedUserId(){
		$session = $GLOBALS["ATK14_GLOBAL"]->getSession();
		// ($user_id = $session->g("fake_logged_user_id")) || // asi bych ukladal pouze skutecne prihlaseneho uzivatele
		($user_id = $session->g("logged_user_id"));
		return $user_id;
	}

	static function FindAll(){
		TableRecord::_NormalizeOptions(func_get_args(),$options);
		if(in_array("Rankable", class_implements(get_called_class()))){
			if(isset($options["order_by"])){ // order_by is just an alias for order, we need to handle it
				$options["order"] = $options["order_by"];
				unset($options["order_by"]);
			}
			$options += array(
				"order" => "rank, id",
			);
		}
		return parent::FindAll($options);
	}

	static function FindFirst(){
		TableRecord::_NormalizeOptions(func_get_args(),$options);
		if(in_array("Rankable", class_implements(get_called_class()))){
			if(isset($options["order_by"])){ // order_by is just an alias for order, we need to handle it
				$options["order"] = $options["order_by"];
				unset($options["order_by"]);
			}
			$options += array(
				"order" => "rank, id",
			);
		}
		return parent::FindFirst($options);
	}

	function getSlugSegment() { return '';}

	/**
	 * echo $product->getSlugPattern("cs"); // Růžové mýdlo
	 * echo $product->getSlugPattern("en"); // Pink Soap
	 */
	function getSlugPattern($lang){ return ''; }
}
