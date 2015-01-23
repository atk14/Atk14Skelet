<?php
/**
 * The base class of all the application db table based models.
 * Do you have any common methods or attributes for all your models? Put them right here.
 */
class ApplicationModel extends TableRecord{

	static $automatically_sluggable = false; // pouziva tato trida slugy?

	function __construct($table_name = null,$options = array()){
		parent::__construct($table_name,$options);
	}

	static function CreateNewRecord($values,$options = array()){
		global $ATK14_GLOBAL;
		$class_name = get_called_class();
		$obj = new $class_name();

		$tr_strings = array();
		if(in_array("Translatable", class_implements($class_name)) && $class_name::GetTranslatableFields()){
			foreach($values as $k => $v){
				if(preg_match('/^(.+)_([a-z]{2})$/',$k,$matches) && in_array($matches[1],$class_name::GetTranslatableFields())){
					$tr_strings[$k] = $v;
					unset($values[$k]);
					continue;
				}
			}
		}

		$slugs = array();
		if($class_name::$automatically_sluggable){
			foreach($values as $k => $v){
				if(preg_match('/^slug_([a-z]{2})$/',$k,$matches)){
					$slugs[$matches[1]] = $v;
					unset($values[$k]);
					continue;
				}
			}
		}

		if($obj->hasKey("created_by_user_id") && !in_array("created_by_user_id",array_keys($values))){
			$values["created_by_user_id"] = ApplicationModel::_GetLoggedUserId();
		}

		$out = parent::CreateNewRecord($values,$options);

		if($tr_strings){
			Translation::SetObjectStrings($out,$tr_strings);
		}

		if($slugs){
			$out->setSlug($slugs);
		}

		if($class_name::$automatically_sluggable){
			Slug::ComplementSlugs($out);
		}

		return $out;
	}

	function toArray(){
		global $ATK14_GLOBAL;
		$class_name = get_class($this);
		$defaults = array();

		if(in_array("Translatable", class_implements($class_name)) && $class_name::GetTranslatableFields()){
			foreach($class_name::GetTranslatableFields() as $k){
				foreach($ATK14_GLOBAL->getSupportedLangs() as $l){
					$defaults["{$k}_$l"] = null;
				}
			}
		}

		$slugs = array();
		if($class_name::$automatically_sluggable){
			foreach($ATK14_GLOBAL->getSupportedLangs() as $l){
				$defaults["slug_$l"] = $this->getSlug($l);
			}
		}

		return parent::toArray() + $slugs + Translation::GetObjectStrings($this) + $defaults;
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

		if($this->hasKey("updated_by_user_id") && !in_array("updated_by_user_id",$v_keys)){
			$values["updated_by_user_id"] = ApplicationModel::_GetLoggedUserId();
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
		$slug_segment = $this->getSlugSegment();
		if($class_name::$automatically_sluggable){
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

		if($class_name::$automatically_sluggable){
			if($slug_segment!==$this->getSlugSegment()){
				$slugs += $original_slugs; // je nutne opetovne nastavit i slugy tech jazyku, ktere ve $values nejsou
			}
			if($slugs){
				// slugy se nastavuji az tady, protoze se muzem volani setValues() zmenit segment
				$this->setSlug($slugs);
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
	 * $slugs = $static_page->getSlugs(); // array("cs" => "vitejte", "en" => "welcome")
	 * $slugs = $static_page->getSlugs(array("prefix" => "slug_")); // array("slug_cs" => "vitejte", "slug_en" => "welcome")
	 * $slugs = $static_page->getSlugs(array("segment" => "123"));
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

	function getSlugSegment(){
		return '';
	}

	/**
	 * echo $product->getSlugPattern("cs"); // Růžové mýdlo
	 * echo $product->getSlugPattern("en"); // Pink Soap
	 */
	function getSlugPattern($lang = null){
		return '';
	}

	/**
	 * $obj->setSlug("pocitac-amiga-1200");
	 * $obj->setSlug("pocitac-amiga-1200","cs");
	 * $obj->setSlug(array(
	 *	"cs" => "pocitac-amiga-1200",
	 *	"en" => "computer-amiga-1200"
	 * ));
	 */
	function setSlug($slug,$lang = null,$segment = null){
		if(is_null($segment)){
			$segment = $this->getSlugSegment();
		}
		$segment = (string)$segment;
		if(is_array($slug)){
			foreach($slug as $lang => $s){
				Slug::SetObjectSlug($this,$s,$lang,$segment);
			}
			return;
		}
		Slug::SetObjectSlug($this,$slug,$lang,$segment);
	}

	static function GetInstanceBySlug($slug,&$lang = null,$segment = ''){
		$class_name = get_called_class();
		$o = new $class_name();
		$table_name = $o->getTableName();

		$record_id = Slug::GetRecordIdBySlug($table_name,$slug,$lang,(string)$segment);
		return Cache::Get("$class_name",$record_id);
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

		$this_class = get_class($this);

		if(!in_array("Translatable", class_implements($this_class))){
			return parent::__call($name,$arguments);
		}

		$_name = new String($name);
		if($_name->match("/^get(.+)/",$matches)){
			$key = $matches[1]->underscore();
			if(in_array($key,$this_class::GetTranslatableFields())){
				$tr_strings = Translation::GetObjectStrings($this);
				$lang = isset($arguments[0]) ? $arguments[0] : $ATK14_GLOBAL->getLang();
				$k = "{$key}_$lang";
				return isset($tr_strings[$k]) ? $tr_strings[$k] : null;
			}
		}

		return parent::__call($name,$arguments);
	}

	function destroy(){
		Translation::DeleteObjectStrings($this);
		Slug::DeleteObjectSlugs($this);
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
			
			if($new_rank===$exp_rank){
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
}

/**
 * Rozhrani pro modely s vicejazycnymi vlastnostmi
 *
 * Model, ktery toto rozhrani implementuje, musi obsahovat statickou metodu GetTranslatableFields
 * vracejici pole s nazvy db policek umoznujici pouzit vice jazyku.
 *
 * Priklad
 * 	class Category extends TableRecord implements Translatable {
 * 		static function GetTranslatableFields() {
 * 			return array("name", "description");
 * 		}
 * 	}
 */
interface Translatable {
	public static function GetTranslatableFields();
}

/**
 * Rozhrani pro objekty, ktere jsou trideny podle policka rank.
 *
 * Metody jako Class::FindAll(), Class::FindFirst(), Class::FindBySomething().... budou defaultne tridit podle policka rank
 */
interface Rankable {
	public function setRank($rank);
}
