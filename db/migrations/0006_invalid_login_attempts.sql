CREATE SEQUENCE seq_invalid_login_attempts;
CREATE TABLE invalid_login_attempts (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_invalid_login_attempts'),
	--
	login VARCHAR(255) NOT NULL,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_from_addr VARCHAR(255) NOT NULL
);
CREATE INDEX in_invalidloginattempts_createdfromaddrcreatedat ON invalid_login_attempts (created_from_addr,created_at);
