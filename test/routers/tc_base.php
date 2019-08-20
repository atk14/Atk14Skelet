<?php
class TcBase extends TcAtk14Router {

	function setUp(){
		$this->dbmole->begin();
		$this->setUpFixtures();
	}

	function tearDown(){
		$this->dbmole->rollback();
	}
}
