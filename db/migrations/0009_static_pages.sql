CREATE SEQUENCE seq_static_pages;
CREATE TABLE static_pages (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_static_pages'),
	--
	parent_static_page_id INT,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_staticpages_parent_staticpages FOREIGN KEY (parent_static_page_id) REFERENCES static_pages ON DELETE CASCADE,
	CONSTRAINT fk_staticpages_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_staticpages_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
