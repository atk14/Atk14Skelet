CREATE SEQUENCE seq_attachments;
CREATE TABLE attachments (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_attachments'),
	--
	table_name VARCHAR(255) NOT NULL, -- products, pages...
	record_id INT NOT NULL,
	section VARCHAR(255) NOT NULL DEFAULT '',
	rank INT NOT NULL DEFAULT 999,
	--
	url VARCHAR(255) NOT NULL,
	filename VARCHAR(255) NOT NULL,
	filesize INT NOT NULL,
	mime_type VARCHAR(255) NOT NULL,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_attachments_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_attachments_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
