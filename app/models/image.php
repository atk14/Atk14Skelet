<?php
class Image extends LinkedObject implements Translatable{

	static function GetTranslatableFields(){ return array("name","description"); }

	static function CreateNewRecord($values,$options = array()){
		myAssert(
			(get_called_class()=="Image" && $values["table_name"]!="products") ||
			(get_called_class()=="ProductImage" && $values["table_name"]=="products")
		);
		return parent::CreateNewRecord($values,$options);
	}

	/**
	 *
	 * $images = Image::GetImages($product);
	 * $images = Image::GetImages($product,"");
	 * $images = Image::GetImages($product,"secondary_images");
	 */
	static function GetImages($obj,$options = array()){
		$class_name = "Image";
		is_a($obj,"Product") && ($class_name = "ProductImage");

		return $class_name::GetInstancesFor($obj,$options);
	}

	/**
	 * Image::AddImage($product,array("url" => "http://...."));
	 * Image::AddImage($product,array("url" => $pupiq));
	 *
	 * Image::AddImage($product,"http://....");
	 */
	static function AddImage($obj,$values,$options = array()){
		if(is_string($values)){
			$values = array("url" => $values);
		}

		$class_name = "Image";
		is_a($obj,"Product") && ($class_name = "ProductImage");
		return $class_name::CreateNewFor($obj,$values,$options);
	}

	function toString(){ return $this->g("url"); }

	function getOriginalWidth(){
		return $this->_getPupiq()->getOriginalWidth();
	}

	function getOriginalHeight(){
		return $this->_getPupiq()->getOriginalHeight();
	}

	function _getPupiq(){
		if(!isset($this->_pupiq)){
			$this->_pupiq = new Pupiq($this->getUrl());
		}
		return $this->_pupiq;
	}

	static function DeleteObjectImages($obj){
		foreach(Image::GetImages($obj) as $i){
			$i->destroy();
		}
	}
}
