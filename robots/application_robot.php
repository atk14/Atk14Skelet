<?php
/**
 * The base class for every other robot.
 */
class ApplicationRobot extends Atk14Robot{
	function beforeRun(){
		$this->dbmole->begin(array(
			"execute_after_connecting" => true
		));
	}

	function afterRun(){
		$this->dbmole->commit();
	}
}
