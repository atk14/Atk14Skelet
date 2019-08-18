-- spolecny zaklad pro objekty vkladanych do textu
CREATE SEQUENCE seq_iobjects;
CREATE TABLE iobjects (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_iobjects'),
 	referred_table VARCHAR(255) NOT NULL, -- 'videos', 'galleries'...
	--
	title_visible BOOLEAN NOT NULL DEFAULT TRUE, -- Ma se nazev objektu zobrazovat navstevnikum na webu?
	-- samotny title je vicejazycny (Translatable)
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_iobjects_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_iobjects_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);

-- one iobject could be linked to multiple objects
CREATE SEQUENCE seq_iobject_links;
CREATE TABLE iobject_links (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_iobject_links'),
	iobject_id INT NOT NULL,
	linked_table VARCHAR(255) NOT NULL,
	linked_record_id INT NOT NULL,
	rank INT NOT NULL DEFAULT 999,
	--
	CONSTRAINT unq_iobjectlinks UNIQUE (iobject_id,linked_table,linked_record_id)
	--
	-- CONSTRAINT fk_iobjectlinks_iobjects FOREIGN KEY (iobject_id) REFERENCES iobjects ON DELETE CASCADE
);
CREATE INDEX in_iobjectlinks_linkedteble_recordid ON iobject_links (linked_table,linked_record_id);

-- obrazky
CREATE TABLE pictures (
	url VARCHAR(255) NOT NULL,
	CONSTRAINT pictures_pkey PRIMARY KEY (id)
) INHERITS (iobjects);

-- videa
CREATE TABLE videos (
	url VARCHAR(255) NOT NULL,
	image_url VARCHAR(255),
	html text NOT NULL,
	autoplay BOOLEAN DEFAULT false NOT NULL,
	loop BOOLEAN DEFAULT false NOT NULL,
	CONSTRAINT videos_pkey PRIMARY KEY (id)
) INHERITS (iobjects);

-- toto je pro do textu vkladane prilohy - jako attachments
CREATE TABLE files (
	url VARCHAR(255) NOT NULL,
	filename VARCHAR(255) NOT NULL,
	filesize INT NOT NULL,
	mime_type VARCHAR(255) NOT NULL,
	CONSTRAINT files_pkey PRIMARY KEY (id)
) INHERITS (iobjects);

-- fotogalerie
CREATE TABLE galleries (
	CONSTRAINT galleries_pkey PRIMARY KEY (id)
) INHERITS (iobjects);

CREATE SEQUENCE seq_gallery_items;
CREATE TABLE gallery_items (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_gallery_items'),
	gallery_id INT NOT NULL,
	rank INT NOT NULL DEFAULT 999,
	image_url VARCHAR(255),
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_galleryitems_galleries FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE,
	CONSTRAINT fk_galleryitems_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_galleryitems_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
CREATE INDEX in_galleryitems_galleryid ON gallery_items(gallery_id);
