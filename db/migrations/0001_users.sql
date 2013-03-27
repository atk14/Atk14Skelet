CREATE SEQUENCE seq_users START WITH 2;
CREATE TABLE users(
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_users'),
	login VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255),
	name VARCHAR(255),
	email VARCHAR(255),
	is_admin BOOLEAN NOT NULL DEFAULT 'f',
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

INSERT INTO users (id,login,password,is_admin) VALUES(1,'admin','admin','t');
