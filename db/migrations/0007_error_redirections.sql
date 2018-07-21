CREATE SEQUENCE seq_error_redirections;
CREATE TABLE error_redirections(
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_error_redirections'),
	--
	source_url VARCHAR NOT NULL,
	target_url VARCHAR NOT NULL,
	regex BOOLEAN NOT NULL DEFAULT FALSE,
	moved_permanently BOOLEAN NOT NULL DEFAULT TRUE,
	--
	last_accessed_at TIMESTAMP,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT unq_errorredirections_sourceurl UNIQUE (source_url),
	CONSTRAINT fk_errorredirections_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_errorredirections_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
