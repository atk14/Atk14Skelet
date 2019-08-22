CREATE SEQUENCE seq_link_lists;
CREATE TABLE link_lists(
	id INTEGER PRIMARY KEY DEFAULT nextval('seq_link_lists'),
	--
	code VARCHAR(255),
	--
	-- orientacni nazev - v adminu
	name VARCHAR(255) NOT NULL,
	--
	url VARCHAR(1000) NOT NULL,
	url_params JSON, -- parametry pro sestaveni URL pomoci Atk14Url::BuildLink(), je-li to vubec mozne
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT unq_linklists_code UNIQUE (code),
	CONSTRAINT fk_linklists_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_linklists_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);


CREATE SEQUENCE seq_link_list_items;
CREATE TABLE link_list_items(
	id INTEGER PRIMARY KEY DEFAULT nextval('seq_link_list_items'),
	--
	link_list_id INTEGER NOT NULL,
	--
	url VARCHAR(1000) NOT NULL,
	url_params JSON, -- parametry pro sestaveni URL pomoci Atk14Url::BuildLink(), je-li to vubec mozne
	--
	image_url VARCHAR(255),
	--
	rank INTEGER NOT NULL DEFAULT 999,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_linklistitems_linklists FOREIGN KEY (link_list_id) REFERENCES link_lists ON DELETE CASCADE,
	CONSTRAINT fk_linklistitems_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_linklistitems_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
