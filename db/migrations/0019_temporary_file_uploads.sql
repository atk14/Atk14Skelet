CREATE SEQUENCE seq_temporary_file_uploads;
CREATE TABLE temporary_file_uploads (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_temporary_file_uploads'),
	--
	path VARCHAR(255) NOT NULL,
	filename VARCHAR(255) NOT NULL,
	filesize INT NOT NULL, -- this is the total filesize (not just a chunk size)
	mime_type VARCHAR(255) NOT NULL,
	--
	chunked_upload BOOLEAN NOT NULL DEFAULT FALSE,
	bytes_uploaded INT,
	last_chunk_uploaded_at TIMESTAMP,
	--
	created_from_addr VARCHAR(255),
	created_from_hostname VARCHAR(255),
	created_by_user_id INT,
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	--
	CONSTRAINT fk_temporaryfileuploads_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT chk_temporaryfileuploads_chunkedupload CHECK((chunked_upload=FALSE AND bytes_uploaded IS NULL AND last_chunk_uploaded_at IS NULL) OR (chunked_upload=TRUE AND bytes_uploaded IS NOT NULL AND last_chunk_uploaded_at IS NOT NULL))
);
