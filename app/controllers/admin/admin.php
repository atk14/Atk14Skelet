<?php
require_once(dirname(__FILE__)."/../application_base.php");

class AdminController extends ApplicationBaseController{
	function access_denied(){
		$this->response->setStatusCode(403);

		$this->page_title = _("Access denied");
		$this->template_name = "shared/access_denied";
	}

	function _application_before_filter(){
		parent::_application_before_filter();

		if(!$this->logged_user || !$this->logged_user->isAdmin()){
			return $this->_execute_action("access_denied");
		}
	}
}
