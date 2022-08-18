<?php
class TestsController extends ApplicationController {

	function index(){
		$this->page_title = "Testování komponent";
	}

	function extended_password_field(){
		$this->page_title = "Extended Password Field";

		$this->form_2 = $this->tpl_data["form_2"] = $this->_get_form("ExtendedPasswordFieldSecondForm");

		if($this->request->post()){

			$form = $this->params->defined("password_1") ? $this->form : $this->form_2;
			if($form->validate($this->params)){
				// ....
			}
		}
	}

	function _before_render(){
		parent::_before_render();

		$this->breadcrumbs[] = ["Testování",$this->_link_to("tests/index")];
		if($this->action!="index"){
			$this->breadcrumbs[] = $this->page_title;
		}
	}
}
