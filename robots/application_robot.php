<?php
/**
 * The base class for every other robot.
 */
class ApplicationRobot extends Atk14Robot{

	function beforeRun(){
		if(TEST){ return; } // the begin of the transaction is in the TcBase::_setUp() method
		$this->dbmole->begin(array(
			"execute_after_connecting" => true
		));
	}

	function afterRun(){
		if(TEST){ return; }
		$this->dbmole->commit();
	}

	function _commit(){
		if(TEST){ return; }
		$this->dbmole->commit();
		$this->dbmole->begin();
	}

	function _rollback(){
		$this->dbmole->rollback();
		$this->dbmole->begin();
	}
}
