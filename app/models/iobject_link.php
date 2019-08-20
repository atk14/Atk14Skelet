<?php
class IobjectLink extends ApplicationModel implements Rankable {

	/**
	 * Deletes existing connections of the given objects to iobjects
	 *
	 *	IobjectLink::DeleteInstancesFor($article);
	 *
	 */
	static function DeleteInstancesFor($obj){
		$dbmole = IobjectLink::GetDbmole();
		$dbmole->doQuery("DELETE FROM iobject_links WHERE linked_table=:table_name AND linked_record_id=:record_id",array(
			":table_name" => $obj->getTableName(),
			":record_id" => $obj,
		));
	}

	function setRank($rank){
		return $this->_setRank($rank,array(
			"linked_table" => $this->g("linked_table"),
			"linked_record_id" => $this->g("linked_record_id"),
		));
	}

	/**
	 * Returns the linked object (owner)
	 *
	 * It may return null when the object was deleted from the database.
	 *
	 *	$article = $object_link->getLinkedObject(); // e.g. "Article", "StaticPage"
	 *
	 */
	function getLinkedObject(){
		$class_name = String4::ToObject($this->getLinkedTable())->singularize()->camelize()->toString();
		return Cache::Get($class_name,$this->getLinkedRecordId());
	}
}
