<?php
/**
 * In the current database reindexes all tables and materialized views belonging to the currently connected user
 *
 * It may be useful to run it daily or weekly.
 *
 * To reindex all table in the current schema:
 *
 *	./scripts/robot_runner reindex_database
 *	# or
 *	./scripts/robot_runner --robot=reindex_database
 *
 * To reindex table with the specific name and next tables in alphabetical order:
 *
 *	./scripts/robot_runner --robot=reindex_database --start-with-table=users
 *	# or
 *	./scripts/robot_runner --robot=reindex_database -susers
 */
class ReindexDatabaseRobot extends ApplicationRobot {

	function run(){
		$arguments = getopt("s::",array("start-with-table::"));
		$arguments += array(
			"s" => null,
		);
		$arguments += array(
			"start-with-table" => $arguments["s"],
		);

		$schema = Atk14Migration::GetDatabaseSchema();
		$bind_ar = array(
			":schema" => $schema
		);

		$this->dbmole->setErrorHandler(function($_dbmole){
			throw new Exception("DbMoleException: ".$_dbmole->getErrorMessage());
		});

		$start_with_table = $arguments["start-with-table"];
		$start_with_table_q = "";
		if(strtolower($start_with_table)){
			$start_with_table_q = " WHERE q.tablename>=:start_with_table";
			$bind_ar[":start_with_table"] = "$schema.$start_with_table";
		}

		$concurrently_reindexing = $this->dbmole->getDatabaseType()==="postgresql" && $this->dbmole->getDatabaseServerVersion("as_float")>12.0 && $this->dbmole->getDatabaseClientVersion("as_float")>12.0;

		if($concurrently_reindexing){
			// REINDEX CONCURRENTLY cannot run inside a transaction block
			$this->dbmole->rollback();
		}

		$tables = $this->dbmole->selectIntoArray("
			SELECT tablename FROM (
				SELECT
					schemaname||'.'||tablename AS tablename
				FROM
					pg_tables
				WHERE
					hasindexes AND
					tableowner=(SELECT CURRENT_USER) AND
					schemaname=:schema
				UNION
				SELECT
					schemaname||'.'||matviewname AS tablename
				FROM
					pg_matviews
				WHERE
					hasindexes AND
					matviewowner=(SELECT CURRENT_USER)
			)q$start_with_table_q ORDER BY tablename
		",$bind_ar);

		foreach($tables as $table){
			$retries = 3;
			while(1){
				$exception_thrown = false;
				try {
					$this->dbmole->doQuery($concurrently_reindexing ? "REINDEX TABLE CONCURRENTLY $table" : "REINDEX TABLE $table");
				}catch(Exception $e){
					$exception_thrown = true;
					$message = $e->getMessage();
					if(preg_match('/deadlock detected/',$message)){
						$this->logger->info("deadlock detected while reindexing $table");
						$this->logger->flush();
					}else{
						$this->logger->error($message);
						$this->logger->flush();
						return;
					}
				}

				if(!$exception_thrown){
					$this->logger->info("table $table reindexed".($concurrently_reindexing ? " concurrently" : ""));
					$this->logger->flush();
					!$concurrently_reindexing && $this->_commit();
					break;
				}

				!$concurrently_reindexing && $this->_rollback();

				if($retries<=0){
					$this->logger->info("table $table was NOT reindexed, exiting...");
					exit; // exit and not return! we don't want the robot lock file to be deleted.
				}

				sleep(5);
				$retries--;
			}
		}
	}
}
