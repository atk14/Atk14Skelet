-- a table for various textual information in different languages
CREATE SEQUENCE seq_translations;
CREATE TABLE translations (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_translations'),
	table_name VARCHAR(255) NOT NULL, -- products, cards, articles...
	record_id INT NOT NULL,
	key VARCHAR(255) NOT NULL, -- title, body....
	lang CHAR(2) NOT NULL, -- en, cs, sk...
	body TEXT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT unq_translations UNIQUE(table_name,record_id,key,lang)
);
