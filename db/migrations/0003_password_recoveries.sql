CREATE SEQUENCE seq_password_recoveries;
CREATE TABLE password_recoveries(
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_password_recoveries'),
	user_id INT NOT NULL,
	email VARCHAR(255),
	created_from_addr VARCHAR(255),
	recovered_at TIMESTAMP, -- when was set the new password
	recovered_from_addr VARCHAR(255),
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP
);
