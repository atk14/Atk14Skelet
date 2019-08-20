<?php
class IobjectsController extends AdminController{

	function destroy(){
		$object = $this->iobject->getObject(); // Video, Gallery...
		myAssert($object);
		$this->_destroy($object);
	}

	function _before_filter(){
		$this->_find("iobject");
	}
}
