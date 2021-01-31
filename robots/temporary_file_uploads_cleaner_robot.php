<?php
class TemporaryFileUploadsCleanerRobot extends ApplicationRobot {
	
	function run(){
		class_exists("TemporaryFileUpload"); // make sure that all the relevant constants are defined

		$records = TemporaryFileUpload::FindAll(array(
			"conditions" => "COALESCE(last_chunk_uploaded_at,created_at)<:limit",
			"bind_ar" => array(
				":limit" => date("Y-m-d H:i:s",time() - TEMPORARY_FILE_UPLOADS_MAX_AGE)
			),
		));
		$this->logger->info("records to be deleted: ".sizeof($records));
		foreach($records as $record){
			$record->destroy();
		}
	}
}
