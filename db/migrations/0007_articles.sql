CREATE SEQUENCE seq_articles;
CREATE TABLE articles(
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_articles'),
	title VARCHAR(255),
	body TEXT,
	author_id INT NOT NULL,
	published_at TIMESTAMP NOT NULL DEFAULT NOW(),
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_articles_users FOREIGN KEY (author_id) REFERENCES users ON DELETE SET NULL,
	CONSTRAINT fk_articles_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_articles_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);

-- see http://book.atk14.net/czech/table-record%3Alister/
CREATE SEQUENCE seq_article_tags;
CREATE TABLE article_tags(
	id INTEGER PRIMARY KEY DEFAULT NEXTVAL('seq_article_tags'),
	article_id INTEGER NOT NULL,
	tag_id INTEGER NOT NULL,
	rank INTEGER DEFAULT 999 NOT NULL,
	CONSTRAINT fk_article_tags_articles FOREIGN KEY (article_id) REFERENCES articles ON DELETE CASCADE,
	CONSTRAINT fk_article_tags_tags FOREIGN KEY (tag_id) REFERENCES tags ON DELETE CASCADE
);
CREATE INDEX in_articletags_articleid ON article_tags(article_id);
CREATE INDEX in_articletags_tagid ON article_tags(tag_id);
