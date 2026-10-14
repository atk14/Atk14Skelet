<?php
class IobjectsController extends ApiController {

	/**
	 * ### Objects linked to a record
	 *
	 * Lists objects (pictures, galleries, videos, files...) linked to the given record,
	 * which can be inserted into its text by their insert marks (e.g. [#123 Picture: Rose]).
	 * It is used by the Markdown editor in the administration.
	 */
	function index(){
		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			$class_name = String4::ToObject($d["table_name"])->singularize()->camelize()->toString(); // "pages" -> "Page"
			$object = null;
			if(class_exists($class_name) && is_subclass_of($class_name,"TableRecord")){
				$object = $class_name::GetInstanceById($d["record_id"]);
			}
			if(!$object || $object->getTableName()!==$d["table_name"]){
				$this->_report_fail(_("There is no such record"),404);
				return;
			}

			$out = array();
			foreach(Iobject::GetInstancesFor($object) as $iobject){
				$out[] = $this->_dump_iobject($iobject);
			}
			$this->api_data = $out;
		}
	}

	function _dump_iobject($iobject){
		Atk14Require::Helper("modifier.iobject_type");
		Atk14Require::Helper("modifier.img_url");

		$type_label = $iobject->getObjectType();
		if(preg_match('/title="(.*?)"/',smarty_modifier_iobject_type($iobject),$matches)){
			$type_label = html_entity_decode($matches[1],ENT_QUOTES,"UTF-8"); // "Obrázek"
		}

		$preview_image_url = $iobject->getPreviewImageUrl();

		return array(
			"id" => $iobject->getId(),
			"type" => $iobject->getObjectType(), // "Picture"
			"type_label" => $type_label,
			"title" => (string)$iobject->getTitle(),
			"insert_mark" => $iobject->getInsertMark(), // "[#123 Picture: Rose]"
			"preview_image_url" => $preview_image_url ? smarty_modifier_img_url($preview_image_url,"160x120xcrop") : null,
		);
	}

	function _logged_admin_required(){
		return true;
	}
}
