CREATE SEQUENCE seq_tags START WITH 101;
CREATE TABLE tags (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_tags'),
	--
	code VARCHAR(255), -- alternative key
	--
	tag VARCHAR(255) NOT NULL UNIQUE,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT unq_tags_code UNIQUE (code),
	CONSTRAINT fk_tags_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_tags_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
INSERT INTO tags (id,tag,code) VALUES(1,'news','news');
INSERT INTO translations (table_name,record_id,lang,key,body) VALUES('tags',1,'cs','tag_localized','aktuality');

INSERT INTO tags (id,tag,code) VALUES(2,'action','action');
INSERT INTO translations (table_name,record_id,lang,key,body) VALUES('tags',2,'cs','tag_localized','akce');
