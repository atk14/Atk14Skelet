CREATE SEQUENCE seq_images;
CREATE TABLE images (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_images'),
	--
	table_name VARCHAR(255) NOT NULL, -- cards, products, brands...
	record_id INT NOT NULL,
	section VARCHAR(255) NOT NULL DEFAULT '', -- '', 'secondary', 'logos'
	rank INT NOT NULL DEFAULT 999,
	--
	url VARCHAR(255) NOT NULL,
	--
	created_by_user_id INT,
	updated_by_user_id INT,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	--
	CONSTRAINT fk_images_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users,
	CONSTRAINT fk_images_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users
);
