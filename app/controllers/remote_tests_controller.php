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
		$tests = array_diff($matches[1],array("index")); // we don't want to action "index" to be listed

		$_tests = [];
		foreach($tests as $test){
			$_tests[] = [
				"name" => $test,
				"url" => $this->_link_to(["action" => $test],["with_hostname" => true]),
			];
		}
		$tests = $_tests;

		if($this->params->defined("format")){
			$this->render_template = false;
			switch($this->params->getString("format")){
				case "json":
					$this->response->setContentType("application/json");
					$this->response->write(json_encode($tests));
					return;
				default:
					$this->response->notFound();
					return;
			}
		}
		$this->tpl_data["tests"] = $tests;
		$this->render_layout = false;
	}
	
	/**
	 * Sample positive test
	 */
	function success(){
		$this->_assert_true(true,"","ok (note the HTTP 200 response status code)");
		//$this->_assert_true(true);
		//$this->_assert_equals(123,123);
	}

	/**
	 * Sample negative test
	 */
	function fail(){
		$this->_fail("fail (note the HTTP 500 response status code)");
		//$this->_assert_equals(123,456);
		//$this->_assert_true(false);
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
			"pattern" => '/(php_error\.log|exception\.log|error\.log)$/',
			"min_mtime" => time() - 30 * 60, // not older than 30 minutes
		));
	}

	/**
	 * Filters out Tracy's exception log files which are no older than 30 minutes
	 */
	function php_exceptions(){
		$this->_check_for_files(LOG_DIR,array(
			"pattern" => '/exception\.log$/',
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

	function disk_space(){
		$kB = 1024;
		$MB = $kB * $kB;
		$GB = 1024 * $MB;

		$disk_space_required = 1 * $GB;

		$this->_assert_true($disk_space_required>0,'bad $disk_space_required');
		$space = disk_free_space(ATK14_DOCUMENT_ROOT);
		$this->_assert_true(is_numeric($space),'disk_free_space() returns no number');
		$this->_assert_true($space > $disk_space_required);
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

	function _assert_equals($expected,$value,$fail_message = "",$success_message = ""){
		if($expected!==$value){
			$this->test_ok = false;
			$this->test_messages[] = $fail_message ? $fail_message : "fail";
		}elseif($success_message){
			$this->test_messages[] = $success_message;
		}
	}

	function _assert_true($expression,$fail_message = "",$success_message = ""){
		return $this->_assert_equals(true,$expression,$fail_message,$success_message);
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
		$files = array_filter($files,function($file){ return filesize($file)>1; }); // fresh files with zero size creates log rotate

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
