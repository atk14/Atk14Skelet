<?php
class TcBase extends TcAtk14Field{

	function setUp(){
		$this->dbmole->begin();
		$this->setUpFixtures();
	}

	function tearDown(){
		$this->dbmole->rollback();
	}
}
