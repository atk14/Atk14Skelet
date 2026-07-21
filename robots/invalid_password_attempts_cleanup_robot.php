<?php
class InvalidPasswordAttemptsCleanupRobot extends ApplicationRobot {

	function run(){
		$records_deleted = InvalidPasswordAttempt::DeleteOldRecords();
		$this->logger->info("records deleted: $records_deleted");
	}
}
