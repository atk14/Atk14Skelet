CREATE SEQUENCE seq_tags START WITH 2;
CREATE TABLE tags (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_tags'),
	tag VARCHAR(255) NOT NULL UNIQUE,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_tags_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_tags_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
INSERT INTO tags (id,tag) VALUES(1,'news');
