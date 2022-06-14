<?php
class Iobject extends ApplicationModel implements Translatable {

	function __construct(){
		parent::__construct(array(
			"sequence_name" => "seq_iobjects"
		));
	}

	static function GetTranslatableFields(){ return array("title", "description"); }

	/**
	 * Creates new Iobject of the given class and connect it to the given object
	 *
	 *	$gallery = Gallery::CreateNewFor($article,["title" => "..."]);
	 *
	 * It may be written as:
	 *
	 *	$gallery = Gallery::CreateNewFor(["title" => "..."]);
	 *	$gallery->linkToObject($article);
	 *
	 */
	static function CreateNewFor($obj, $values = array(),$options = array()) {
		$iobject = self::CreateNewRecord($values,$options);
		IobjectLink::CreateNewRecord(array(
			"iobject_id" => $iobject,
			"linked_table" => $obj->getTableName(), // e.g. "galleries"
			"linked_record_id" => $obj->getId(), // 123
		));
		return $iobject;
	}

	/**
	 * Returns connected Iobjects to the given $object
	 *
	 *	$galleries = Gallery::GetInstancesFor($article); // Gallery[]
	 *	$galleries = Picture::GetInstancesFor($article); // Picture[]
	 *	$iobjects = Iobject::GetInstancesFor($article); // Iobject[]
	 *
	 * @param $obj LinkedObject
	 * @param $options @see TableRecord::Find
	 * @return Iobject[]
	 */
	static function GetInstancesFor($obj, $options = array()){
		if (is_null($obj)) {
			return null;
		}

		$class_name = get_called_class(); // e.g. "Gallery"
		$_o = new $class_name();
		$_table = $_o->getTableName();

		$options += [
			"conditions" => [],
			"bind_ar" => [],
			"order_by" => "(SELECT rank FROM iobject_links WHERE linked_table=:linked_table AND linked_record_id=:linked_record_id AND iobject_id=$_table.id), id"
		];

		$options["conditions"][] = "id IN (SELECT iobject_id FROM iobject_links WHERE linked_table=:linked_table AND linked_record_id=:linked_record_id)";

		if($class_name!="Iobject"){
			$options["conditions"][] = "referred_table=:referred_table";
		}

		$options["bind_ar"][":linked_table"] = $obj->getTableName();
		$options["bind_ar"][":linked_record_id"] = $obj->getId();
		$options["bind_ar"][":referred_table"] = $_o->getTableName(); // e.g. "galleries"

		return static::FindAll($options);
	}


	/**
	 * $iobjects = Iobject::GetInstances($card_section);
	 */
	static function GetIobjects($obj){
		return self::GetInstancesFor($obj);
	}

	static function CreateNewRecord($values,$options = array()){
		$class_name = get_called_class();
		$obj = new $class_name();
		$values["referred_table"] = $obj->getTableName(); // Video::CreateNewRecord(array("title" => "Bomba")); -> "referred_table"="videos"

		return parent::CreateNewRecord($values,$options);
	}

	/**
	 *	$picture = Picture::CreateNewRecord([...]); // an Iobject
	 * 	$picture->linkToObject($picture);
	 */
	function linkToObject($obj){
		return IobjectLink::CreateNewRecord(array(
			"iobject_id" => $this,
			"linked_table" => $obj->getTableName(),
			"linked_record_id" => $obj,
		));
	}

	function getObject(){
		$class_name = $this->_getObjectClassName();
		return Cache::Get($class_name,$this->getId());
	}

	function getIobjectLink($object){
		return IobjectLink::FindFirst("iobject_id",$this,"linked_table",$object->getTableName(),"linked_record_id",$object);
	}

	function getIobjectLinkId($object){
		if($iobject_link = $this->getIobjectLink($object)){
			return $iobject_link->getId();
		}
	}

	/**
	 * Je nazev objektu viditelny pro navstevniky na strankach?
	 */
	function isTitleVisible(){
		return $this->g("title_visible");
	}

	/**
	 * Vrati znacku, kterou je mozne vlozit do clanku a za ni je pak nasledne nahrazen obsah daneho Iobjectu
	 *
	 * echo $video->getInsertMark(); // "[#123 Video: Arnold jede na kole]"
	 */
	function getInsertMark(){
		$type = $this->getObjectType(); // "galleries" -> "Gallery"
		$title = strip_tags((string)$this->getTitle());
		$title = preg_replace('/[\[\]\n\r]/','_',$title);
		$title = trim($title);
		if(!strlen($title)){
			return sprintf('[#%s %s]',$this->getId(),$this->_getObjectClassName()); // [#123 Video]
		}
		return sprintf('[#%s %s: %s]',$this->getId(),$this->_getObjectClassName(),$title); // [#123 Video: Arnold jede na kole]
	}

	/**
	 * Returns HTML snippet to be inserted into a content (article, page...)
	 */
	function getHtmlSource($options = array()){
		$smarty = Atk14Utils::GetSmarty(array(
			ATK14_DOCUMENT_ROOT."app/views/"
		));
		Atk14Require::Helper("function.iobject_to_html",$smarty);
		return smarty_function_iobject_to_html(array("iobject" => $this), $smarty);
		//return sprintf('<div style="background-color: red;">%s %s</div>',$this->_getObjectClassName(),$this->getId());
	}

	/**
	 * A public URL of detail of the given object
	 *
	 * If the object is an image, it could be a link to the image itself.
	 *
	 * If the object is a video, it could be an external link to the video on YouTube, Vimeo, etc.
	 */
	function getDetailUrl(){
		$object = $this->getObject();
		if(method_exists($object,"getUrl")){
			return $object->getUrl();
		}
		if(is_a($object,"TableRecord") && $object->hasKey("url")){
			return $object->g("url");
		}

		$controller = String4::ToObject(get_class($object))->underscore()->pluralize()->toString(); // "Gallery" -> "galleries"
		return Atk14Url::BuildLink(array(
			"namespace" => "",
			"controller" => $controller,
			"action" => "detail",
			"id" => $object,
		));
	}

	/**
	 *
	 * $class_name = $this->_getObjectClassName(); // e.g. "Gallery"
	 */
 	function _getObjectClassName(){
		return String4::ToObject($this->getReferredTable())->singularize()->camelize()->toString(); // "galleries" -> "Gallery"
	}

	/**
	 *
	 * echo $iobject->getObjectType(); // e.g. "Gallery"
	 */
	function getObjectType(){
		return $this->_getObjectClassName();
	}

	function getTitle(){
		if(get_class($this)=="Iobject"){
			// je mozne, ze si dedic implementuje getTitle() nejak po svem
			return $this->getObject()->getTitle();
		}
		return parent::getTitle();
	}

	function getPreviewImageUrl(){
		if(get_class($this)=="Iobject"){
			return $this->getObject()->getPreviewImageUrl();
		}
	}

	function getCreatedByUser(){
		return Cache::Get("User",$this->getCreatedByUserId());
	}

	function getUpdatedByUser(){
		return Cache::Get("User",$this->getUpdatedByUserId());
	}

	function prepareFulltextData(&$fulltext_ar){
		Fulltext::Append($fulltext_ar,$this->getTitle());
		if($this->hasKey("description")){
			Fulltext::Append($fulltext_ar,$this->getDescription());
		}
	}

	function destroy($destroy_for_real = null){
		$iobject_id = $this->getId();

		$out = parent::destroy($destroy_for_real);

		// toto je osetreni toho, ze iobject_links.iobject_id neni foreign key na iobjects.id
		foreach(IobjectLink::FindAll("iobject_id",$iobject_id) as $l){
			$l->destroy();
		}

		return $out;
	}
}
