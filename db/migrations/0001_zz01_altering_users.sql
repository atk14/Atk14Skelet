ALTER TABLE users ADD created_by_user_id INT;
ALTER TABLE users ADD CONSTRAINT fk_users_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users;
