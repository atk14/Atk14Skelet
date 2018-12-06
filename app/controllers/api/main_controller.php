<?php
class MainController extends ApiController{
	function index(){
		$this->_render_command_list(__DIR__);
	}
}
