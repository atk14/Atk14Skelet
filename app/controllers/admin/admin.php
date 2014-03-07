<?php
require_once(dirname(__FILE__)."/../application_base.php");

class AdminController extends ApplicationBaseController{
	function _application_before_filter(){
		parent::_application_before_filter();

		if(!$this->logged_user || !$this->logged_user->isAdmin()){
			return $this->_execute_action("error403");
		}

		$navi = new Navigation();
		$navi->add(_("Welcome screen"),$this->_link_to("main/index"),array("active" => $this->controller=="main"));
		$navi->add(_("Users"),$this->_link_to("users/index"),array("active" => $this->controller=="users"));
		$navi->add(_("News"),$this->_link_to("news/index"),array("active" => $this->controller=="news"));
		$navi->add(_("Password recoveries"),$this->_link_to("password_recoveries/index"),array("active" => $this->controller=="password_recoveries"));
		$this->tpl_data["section_navigation"] = $navi;
	}
}
