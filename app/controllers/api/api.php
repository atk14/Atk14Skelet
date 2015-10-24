<?php
require_once(dirname(__FILE__)."/../application_base.php");
require_once(dirname(__FILE__)."/../application_rest_api.php");
require_once(dirname(__FILE__)."/../../../lib/markdown.php");

class ApiController extends ApplicationRestApiController{
	function _dump_logged_user(){
		return $this->_dump_user($this->_get_logged_user());
	}

	function _dump_user($user){
		return $user ? array(	
			"id" => $user->getId(),
			"login" => $user->getLogin(),
			"name" => $user->getName(),
			"email" => $user->getEmail(),
			"is_admin" => $user->isAdmin()
		) : array();
	}

	function _application_before_filter(){
		parent::_application_before_filter();

		if($this->controller=="main" && $this->action=="index"){
			$this->breadcrumbs[] = $this->namespace; // "api"
		}else{
			$this->breadcrumbs[] = array($this->namespace,$this->_link_to(array("namespace" => $this->namespace, "controller" => "main", "action" => "index")));
			$this->breadcrumbs[] = "$this->controller/$this->action"; // "logins/detail"
		}
	}
}
