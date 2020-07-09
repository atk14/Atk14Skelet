ALTER TABLE users ADD last_signed_in_at TIMESTAMP;
ALTER TABLE users ADD last_signed_in_from_addr VARCHAR(255);
ALTER TABLE users ADD last_signed_in_from_hostname VARCHAR(255);
ALTER TABLE users ADD password_changed_at TIMESTAMP;
ALTER TABLE users ADD password_administratively_changed_at TIMESTAMP;
