-- a more general structure than invalid_login_attempt
CREATE SEQUENCE seq_invalid_password_attempts;
CREATE TABLE invalid_password_attempts (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_invalid_password_attempts'),
	--
	purpose VARCHAR(255) NOT NULL, -- e.g. 'user_login', 'user_login_2fa', 'email_confirmation'
	object_key VARCHAR(255) NOT NULL, -- e.g. 'john.doe', '123', 'john@doe.com'
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_from_addr VARCHAR(255) NOT NULL,
	created_from_hostname VARCHAR(255)
);
CREATE INDEX in_invalidpasswordattempts_purposeobjectkey ON invalid_password_attempts (purpose,object_key);
