-- a table for storing unique slugs for objects of different types
CREATE SEQUENCE seq_slugs;
CREATE TABLE slugs (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_slugs'),
	table_name VARCHAR NOT NULL, -- pages, categories, brands...
	record_id INT NOT NULL,
	segment VARCHAR NOT NULL DEFAULT '', -- a differentiation for the uniqueness
	slug VARCHAR NOT NULL, -- about-us, our-vision, our-mission
	lang CHAR(2) NOT NULL, -- en, cs, sk...
	--
	CONSTRAINT unq_slugs_records UNIQUE (table_name,record_id,lang),
	CONSTRAINT unq_slugs_slugs UNIQUE (table_name,slug,segment,lang)
);
