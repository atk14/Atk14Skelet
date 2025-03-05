<?php
/**
 * The base class for every other robot.
 */
class ApplicationRobot extends Atk14Robot{

	function beforeRun(){
		if(TEST){ return; }
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
}
