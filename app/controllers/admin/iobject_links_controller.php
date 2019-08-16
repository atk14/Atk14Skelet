<?php
class IobjectLinksController extends AdminController {

	/**
	 * Smaze pouze napojeni na Iobject
	 */
	function destroy(){
		$this->_destroy();
	}

	function set_rank(){
		$this->_set_rank();
	}
}
