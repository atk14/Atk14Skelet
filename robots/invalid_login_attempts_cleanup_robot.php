<?php
class InvalidLoginAttemptsCleanupRobot extends ApplicationRobot {

	function run(){
		$records_deleted = InvalidLoginAttempt::DeleteOldRecords();
		$this->logger->info("records deleted: $records_deleted");
	}
}
