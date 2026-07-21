CREATE SEQUENCE seq_one_time_passwords;
CREATE TABLE one_time_passwords (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_one_time_passwords'),
	--
	purpose VARCHAR(255) NOT NULL, -- e.g. 'user_login_2fa', 'email_confirmation'
	object_key VARCHAR(255) NOT NULL, -- e.g. '123', 'john@doe.com'
	--
	recipient VARCHAR(255) NOT NULL, -- e.g. 'john@doe.com', '+420.605123456'
	--
	password VARCHAR(255) NOT NULL,
	expires_at TIMESTAMP NOT NULL,
	--
	used_at TIMESTAMP,
	used_from_addr VARCHAR(255),
	used_from_hostname VARCHAR(255),
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP DEFAULT NOW() NOT NULL,
	created_from_addr VARCHAR(255),
	created_from_hostname VARCHAR(255),
	--
	CONSTRAINT chk_onetimepasswords_used CHECK (
		(used_at IS NULL AND used_from_addr IS NULL AND used_from_hostname IS NULL) OR
		(used_at IS NOT NULL AND used_from_addr IS NOT NULL AND used_from_hostname IS NOT NULL)
	),
	CONSTRAINT fk_onetimepasswords_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_onetimepasswords_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
CREATE INDEX in_onetimepasswords_purpose ON one_time_passwords(purpose,object_key);
