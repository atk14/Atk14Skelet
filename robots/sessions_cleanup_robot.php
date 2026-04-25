<?php
class SessionsCleanupRobot extends ApplicationRobot {

	function run(){
		$records_deleted = SessionStorer::DeleteOldSessions();
		$this->logger->info("records deleted: $records_deleted");
	}
}
