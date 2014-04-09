<?php
/**
 * The base class of every controller`s test case class.
 *
 * Notice that TcBase is descendant of TcAtk14Controller
 * so there are a couple of special member variables in advance.
 */
class TcBase extends TcAtk14Controller{

	function setUp(){
		$this->dbmole->begin();
		$GLOBALS["_POST"]["_csrf_token_"] = "testing_csrf_token"; // in tests we do not to deal with the csrf protection
	}

	function tearDown(){
		$this->dbmole->rollback();
	}
}
