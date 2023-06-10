ALTER TABLE users ADD deleted BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE users ADD deleted_at TIMESTAMP;
ALTER TABLE users ADD CONSTRAINT chk_users_deleted CHECK((NOT deleted AND deleted_at IS NULL) OR (deleted AND deleted_at IS NOT NULL));
