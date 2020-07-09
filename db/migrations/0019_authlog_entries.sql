CREATE SEQUENCE seq_authlog_entries;
CREATE TABLE authlog_entries (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_authlog_entries'),
	--
	login VARCHAR(255) NOT NULL,
	--
	user_id INT,
	admin_user BOOLEAN,
	authenticated BOOLEAN NOT NULL,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_from_addr VARCHAR(255),
	created_from_hostname VARCHAR(255),
	created_from_user_agent VARCHAR
);
CREATE INDEX in_authlogentries_login ON authlog_entries (login);
CREATE INDEX in_authlogentries_createdfromaddr ON authlog_entries (created_from_addr);
