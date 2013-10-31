<?php
class TcBase extends TcAtk14Field{

	function setUp(){
		$this->dbmole->begin();
	}

	function tearDown(){
		$this->dbmole->rollback();
	}
}
