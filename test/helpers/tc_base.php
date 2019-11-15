<?php
class TcBase extends TcAtk14Base {

	function setUp(){
		$this->dbmole->begin();
		$this->setUpFixtures();
	}

	function tearDown(){
		$this->dbmole->rollback();
	}
}
