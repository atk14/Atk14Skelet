<?php
/**
 * A sample robot
 */
class DummyRobot extends ApplicationRobot{
	function run(){
		// You have access to
		//	$this->logger
		// 	$this->dbmole
		//	$this->mailer
		//	
		// For more information visit http://book.atk14.net/czech/robots/

		$this->logger->info("Hi. I'm a dummy robot. I'm doing nothing. Bye.");
	}
}
