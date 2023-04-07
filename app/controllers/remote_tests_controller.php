<?php
/**
 * Here are actions for remote testing the application (for example) using Nagios
 *
 * Every action returns HTTP status 200 when the test passed, otherwise it returns HTTP status 500.
 */
class RemoteTestsController extends ApplicationController{

	/**
	 * Displays list of all tests
	 */
	function index(){
		$source = Files::GetFileContent(__FILE__);
		preg_match_all('/function\s+([a-z][a-z0-9_]*)\s*\(/',$source,$matches);
		$this->tpl_data["tests"] = array_diff($matches[1],array("index","fail")); // we don't want to actions "index" and "fail" to be listed
		$this->render_layout = false;
	}
	
	/**
	 * Sample positive test
	 */
	function success(){
		$this->_assert_true(true);
		$this->_assert_equals(123,123);
	}

	/**
	 * Sample negative test
	 */
	function fail(){
		$this->_fail();
		$this->_assert_equals(123,456);
		$this->_assert_true(false);
	}

	/**
	 * Checks for existence of stale locks from robots
	 */
	function stale_locks(){
		$this->_check_for_files(LOCK_DIR,array(
			"max_mtime" => time() - 20 * 60, // older than 20 minutes
			"invert_pattern" => '/(README|\.gitkeep)/'
		));
	}

	/**
	 * Filters out Tracy's log files which are no older than 30 minutes
	 */
	function php_errors(){
		$this->_check_for_files(LOG_DIR,array(
			"pattern" => '/(php_error\.log|exception|error\.log)$/',
			"min_mtime" => time() - 30 * 60, // not older than 30 minutes
		));
	}

	/**
	 * Filters out Tracy's exception log files which are no older than 30 minutes
	 */
	function php_exceptions(){
		$this->_check_for_files(LOG_DIR,array(
			"pattern" => '/exception/',
			"min_mtime" => time() - 30 * 60, // not older than 30 minutes
		));
	}

	/**
	 * Reports fresh changes to the robots error log
	 */
	function robot_errors(){
		$this->_check_for_files(LOG_DIR,array(
			"pattern" => '/(robots_error\.log)$/',
			"min_mtime" => time() - 30 * 60, // not older than 30 minutes
		));
	}

	/**
	 * Test fails when admin has default password
	 */
	function admin_default_password(){
		$this->_assert_true(is_null(User::Login("admin","admin")));
	}

	function _before_filter(){
		/*
		// Here you can restrict access to the controller's actions for listed IP addresses
		if(!in_array($this->request->getRemoteAddr(),array("10.20.30.40"))){
			return $this->_execute_action("error403");
		} // */

		$this->test_ok = true;
		$this->test_messages = array();
	}

	function _assert_equals($expected,$value,$message = ""){
		if($expected!==$value){
			$this->test_ok = false;
			$this->test_messages[] = $message ? $message : "fail";
		}
	}

	function _assert_true($expression,$message = ""){
		return $this->_assert_equals(true,$expression,$message);
	}

	function _fail($messages = ""){
		$this->test_ok = false;
		if($messages){
			if(!is_array($messages)){ $messages = array($messages); }
			$this->test_messages = array_merge($messages,$this->test_messages);
		}
	}

	function _check_for_files($directory,$check_options){
		if(!file_exists($directory)){
			return; // ok....
		}
		if(!is_dir($directory)){
			$this->_fail("$directory doesn't exist or is not directory");
			return;
		}

		$cwd = getcwd();

		chdir($directory);
		$files = Files::FindFiles(".",$check_options);
		array_filter($files,function($file){ return filesize($file)>0; }); // fresh files with zero size creates log rotate

		if($files){
			$this->_fail(join("\n",$files));
		}

		chdir($cwd);
	}

	function _before_render(){
		parent::_before_render();

		if($this->action=="index"){ return; }

		if(!isset($this->test_ok)){
			return;
		}

		if($this->test_ok && !$this->test_messages){ $this->test_messages[] = "ok"; }
		if(!$this->test_ok && !$this->test_messages){ $this->test_messages[] = "fail"; }
	
		$this->render_template = false;
		$this->response->setContentType("text/plain");

		if(!$this->test_ok){
			$this->response->setStatusCode(500);
		}

		$this->response->write(join("\n",$this->test_messages));
	}

}
