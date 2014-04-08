<?php
/**
 * The base class of every controller`s test case class.
 *
 * Notice that TcBase is descendant of TcAtk14Controller
 * so there is a couple of special member variables in advance.
 */
class TcBase extends TcAtk14Controller{

	function setUp(){
		$this->dbmole->begin();
		$this->_loginUser(1); // admin
		$GLOBALS["_POST"]["_csrf_token_"] = "testing_csrf_token"; // in tests we do not to deal with the csrf protection
	}

	function tearDown(){
		$this->dbmole->rollback();
	}

	function _loginUser($user){
		$user = is_object($user) ? $user->getId() : $user;
		$this->client->session->s("logged_user_id",$user);
	}

	function _logoutUser(){
		$this->client->session->clear("logged_user_id");
	}
}
