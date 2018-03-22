<?php
/**
 * In the current database reindexes all tables and materialized views belonging to the currently connected user
 *
 * It may be useful to run it daily or weekly.
 */
class ReindexDatabaseRobot extends ApplicationRobot {

	function run(){
		$tables = $this->dbmole->selectIntoArray("
			SELECT tablename FROM (
				SELECT
					schemaname||'.'||tablename AS tablename
				FROM
					pg_tables
				WHERE
					hasindexes AND
					tableowner=(SELECT CURRENT_USER)
				UNION
				SELECT
					schemaname||'.'||matviewname AS tablename
				FROM
					pg_matviews
				WHERE
					hasindexes AND
					matviewowner=(SELECT CURRENT_USER)
			)q ORDER BY tablename
		");
		foreach($tables as $table){
			$this->dbmole->doQuery("REINDEX TABLE $table");
			$this->logger->info("table $table reindexed");
			$this->logger->flush();
		}
	}
}
