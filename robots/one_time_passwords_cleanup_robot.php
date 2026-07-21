<?php
class OneTimePasswordsCleanupRobot extends ApplicationRobot {

	function run(){
		$threshold_date = date("Y-m-d H:i:s",time() - 60 * 60 * 24 * 7); // 7 days
		$this->dbmole->doQuery("DELETE FROM one_time_passwords WHERE expires_at<=:threshold_date",[
			":threshold_date" => $threshold_date,
		]);
		$records_deleted = $this->dbmole->getAffectedRows();
		$this->logger->info("records deleted: $records_deleted");
	}
}
