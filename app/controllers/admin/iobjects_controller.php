<?php
class IobjectsController extends AdminController{

	/**
	 * Redirects to the editing of the iobject in its own controller (pictures/edit, videos/edit...)
	 *
	 * It is useful when only the id of an iobject is known, e.g. in the Markdown block editor.
	 */
	function edit(){
		$object = $this->iobject->getObject(); // Video, Gallery...
		myAssert($object);
		$controller = String4::ToObject(get_class($object))->underscore()->pluralize()->toString(); // "Picture" -> "pictures"
		$this->_redirect_to(array(
			"controller" => $controller,
			"action" => "edit",
			"id" => $object,
		));
	}

	function destroy(){
		$object = $this->iobject->getObject(); // Video, Gallery...
		myAssert($object);
		$this->_destroy($object);
	}

	function _before_filter(){
		$this->_find("iobject");
	}
}
