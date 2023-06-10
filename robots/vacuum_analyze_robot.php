<?php
/**
 * Performs VACUUM ANALYZE in the current database
 *
 * It may be run, for example, once a week.
 */
class VacuumAnalyzeRobot extends ApplicationRobot {

	function run(){
		$this->dbmole->commit(); // VACUUM ANALYZE cannot be run in a transaction block
		$rows = $this->dbmole->selectRows("VACUUM ANALYZE");
	}
}
