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
		$this->client->setRemoteAddr("127.0.0.1");
	}

	function tearDown(){
		$this->dbmole->rollback();
	}

	function _decodeResponseData($content = null){
		if(!isset($content)){
			$content = $this->client->getContent();
		}
		return json_decode($content,true);
	}

	function _getLastStatusCode(){
		return $this->client->getStatusCode();
	}

	/**
	 * Makes a GET request and returns output data
	 *
	 * $data = $this->_get("users/detail",array("id" => 123));
	 */
	function _get($path,$params = array(),&$response_code = null,&$controller = null){
		$params += array(
			"format" => "json",
		);

		$controller = $this->client->get($path,$params);

		$response_code = $this->client->getStatusCode();
		return $this->_decodeResponseData($this->client->getContent());
	}

	/**
	 * Makes a POST request and returns output data
	 *
	 * $data = $this->_post("users/create_new",array("login" => "john", "password" => "Big.John"));
	 */
	function _post($path,$params = array(),&$response_code = null,&$controller = null){
		$params += array(
			"format" => "json",
		);

		$controller = $this->client->post($path,$params);

		$response_code = $this->client->getStatusCode();
		return $this->_decodeResponseData($this->client->getContent());
	}
}
